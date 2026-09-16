<?php

namespace App\Services\Payments;

use App\Models\PaymentAttempt;
use Illuminate\Http\Request;

/**
 * Paddle Billing driver (international cards, PayPal; Paddle handles tax).
 *
 * Flow: create a Paddle transaction with inline prices built from the cart,
 * redirect the customer to the hosted checkout URL. Paddle sends a
 * transaction.completed webhook which we verify via the Paddle-Signature
 * header (HMAC-SHA256 of "ts:body" with the webhook secret).
 */
class PaddleDriver extends GatewayDriver
{
    protected function baseUrl(): string
    {
        return $this->isTestMode()
            ? 'https://sandbox-api.paddle.com'
            : 'https://api.paddle.com';
    }

    public function initiate(PaymentAttempt $attempt): array
    {
        $items = [];
        foreach ($attempt->cart as $item) {
            $unitCents = (int) round(((float) ($item['price'] ?? 0)) * 100);
            $items[] = [
                'quantity' => (int) ($item['qty'] ?? 1),
                'price' => [
                    'description' => mb_substr($item['title'] ?? 'Product', 0, 255),
                    'unit_price' => [
                        'amount' => (string) $unitCents,
                        'currency_code' => 'USD',
                    ],
                ],
            ];
        }

        $response = $this->http()
            ->withToken($this->cred('api_key'))
            ->post($this->baseUrl() . '/transactions', [
                'items' => $items,
                'customer' => ['email' => $attempt->user->email ?? ''],
                'custom_data' => ['attempt_token' => $attempt->token],
            ]);

        $data = $response->json('data') ?? [];
        $checkoutUrl = $data['checkout']['url'] ?? null;

        if (! $checkoutUrl || ! $response->successful()) {
            \Illuminate\Support\Facades\Log::error('Paddle transaction create failed', ['response' => $response->json()]);
            throw new \RuntimeException('Paddle is not available right now. Please try another payment method.');
        }

        $attempt->update(['gateway_reference' => $data['id'] ?? null]);

        return ['redirect' => $checkoutUrl];
    }

    public function verifyCallback(Request $request): array
    {
        // Paddle returns the customer to our URL; the webhook is the source
        // of truth. If the transaction id is present, verify it via the API.
        $transactionId = $request->query('transaction_id') ?? $request->input('_ptxn');
        $attemptToken = $request->query('ref');

        if ($transactionId && $this->transactionIsPaid($transactionId)) {
            return [
                'ok' => true,
                'attempt_token' => $attemptToken,
                'gateway_reference' => $transactionId,
                'amount' => null,
                'raw' => ['transaction_id' => $transactionId],
            ];
        }

        return $this->fail('transaction not verified', $request->all());
    }

    public function verifyWebhook(Request $request): array
    {
        $signature = $request->header('Paddle-Signature', '');
        if (! $this->validWebhookSignature($request->getContent(), $signature)) {
            return $this->fail('invalid webhook signature');
        }

        $payload = $request->json()->all();
        if (($payload['event_type'] ?? '') !== 'transaction.completed') {
            return ['ok' => false];
        }

        $data = $payload['data'] ?? [];

        return [
            'ok' => true,
            'attempt_token' => $data['custom_data']['attempt_token'] ?? null,
            'gateway_reference' => $data['id'] ?? null,
            'amount' => isset($data['details']['totals']['total'])
                ? ((float) $data['details']['totals']['total'] / 100) : null,
            'raw' => $payload,
        ];
    }

    protected function transactionIsPaid(string $transactionId): bool
    {
        try {
            $response = $this->http()
                ->withToken($this->cred('api_key'))
                ->get($this->baseUrl() . '/transactions/' . $transactionId);

            $status = $response->json('data.status');

            return in_array($status, ['completed', 'paid']);
        } catch (\Throwable) {
            return false;
        }
    }

    protected function validWebhookSignature(string $body, string $header): bool
    {
        $secret = (string) $this->cred('webhook_secret');
        if (! $secret || ! $header) {
            return false;
        }

        // Header format: "ts=...;h1=..."
        $parts = [];
        foreach (explode(';', $header) as $part) {
            [$k, $v] = array_pad(explode('=', $part, 2), 2, '');
            $parts[$k] = $v;
        }

        if (empty($parts['ts']) || empty($parts['h1'])) {
            return false;
        }

        $expected = hash_hmac('sha256', $parts['ts'] . ':' . $body, $secret);

        return hash_equals($expected, $parts['h1']);
    }
}
