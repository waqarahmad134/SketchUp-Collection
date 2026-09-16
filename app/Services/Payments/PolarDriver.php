<?php

namespace App\Services\Payments;

use App\Models\PaymentAttempt;
use Illuminate\Http\Request;

/**
 * Polar.sh driver (hosted checkout for digital products).
 *
 * Flow: create a checkout session for the configured Polar product, redirect
 * the customer to the hosted URL. Polar sends a checkout.updated webhook
 * which we verify with the webhook secret.
 *
 * Setup: create a generic "Store Order" product in Polar and paste its
 * product ID in the gateway settings. The real order total travels in the
 * checkout metadata.
 */
class PolarDriver extends GatewayDriver
{
    protected function baseUrl(): string
    {
        return $this->isTestMode()
            ? 'https://sandbox-api.polar.sh/v1'
            : 'https://api.polar.sh/v1';
    }

    public function initiate(PaymentAttempt $attempt): array
    {
        $response = $this->http()
            ->withToken($this->cred('access_token'))
            ->post($this->baseUrl() . '/checkouts', [
                'products' => [(string) $this->cred('product_id')],
                'metadata' => [
                    'attempt_token' => $attempt->token,
                    'order_total_usd' => (string) $attempt->total,
                ],
                'customer_email' => $attempt->user->email ?? '',
                'success_url' => $this->returnUrl() . '?ref=' . $attempt->token,
            ]);

        $data = $response->json() ?? [];
        $checkoutUrl = $data['url'] ?? null;

        if (! $checkoutUrl || ! $response->successful()) {
            \Illuminate\Support\Facades\Log::error('Polar checkout create failed', ['response' => $data]);
            throw new \RuntimeException('Polar is not available right now. Please try another payment method.');
        }

        $attempt->update(['gateway_reference' => $data['id'] ?? null]);

        return ['redirect' => $checkoutUrl];
    }

    public function verifyCallback(Request $request): array
    {
        // Polar confirms via webhook; verify the checkout via the API on return.
        $checkoutId = $request->query('checkout_id');

        if ($checkoutId && $this->checkoutIsPaid($checkoutId)) {
            return [
                'ok' => true,
                'attempt_token' => $request->query('ref'),
                'gateway_reference' => $checkoutId,
                'amount' => null,
                'raw' => ['checkout_id' => $checkoutId],
            ];
        }

        return $this->fail('checkout not verified', $request->all());
    }

    public function verifyWebhook(Request $request): array
    {
        $secret = (string) $this->cred('webhook_secret');
        $signature = $request->header('Webhook-Signature', $request->header('X-Polar-Signature', ''));

        if ($secret && (! $signature || ! hash_equals(hash_hmac('sha256', $request->getContent(), $secret), $signature))) {
            return $this->fail('invalid webhook signature');
        }

        $payload = $request->json()->all();
        $event = $payload['type'] ?? '';

        if (! in_array($event, ['checkout.updated', 'order.created'])) {
            return ['ok' => false];
        }

        $data = $payload['data'] ?? [];
        $status = strtolower($data['status'] ?? '');

        if (! in_array($status, ['succeeded', 'paid', 'completed'])) {
            return ['ok' => false];
        }

        return [
            'ok' => true,
            'attempt_token' => $data['metadata']['attempt_token'] ?? null,
            'gateway_reference' => (string) ($data['id'] ?? ''),
            'amount' => isset($data['amount']) ? ((float) $data['amount'] / 100) : null,
            'raw' => $payload,
        ];
    }

    protected function checkoutIsPaid(string $checkoutId): bool
    {
        try {
            $response = $this->http()
                ->withToken($this->cred('access_token'))
                ->get($this->baseUrl() . '/checkouts/' . $checkoutId);

            $status = strtolower($response->json('status', ''));

            return in_array($status, ['succeeded', 'paid', 'completed']);
        } catch (\Throwable) {
            return false;
        }
    }
}
