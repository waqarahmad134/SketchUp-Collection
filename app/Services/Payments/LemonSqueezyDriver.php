<?php

namespace App\Services\Payments;

use App\Models\PaymentAttempt;
use Illuminate\Http\Request;

/**
 * Lemon Squeezy driver (hosted checkout for digital products).
 *
 * Flow: create a checkout for the configured store variant, redirect the
 * customer to the hosted URL. Lemon Squeezy sends an order_created webhook
 * signed with the signing secret (X-Signature: HMAC-SHA256 of raw body).
 *
 * Setup: create a generic "Store Order" product in Lemon Squeezy and paste
 * its variant ID in the gateway settings. The real order total travels in
 * the checkout's custom metadata.
 */
class LemonSqueezyDriver extends GatewayDriver
{
    protected function baseUrl(): string
    {
        return 'https://api.lemonsqueezy.com/v1';
    }

    public function initiate(PaymentAttempt $attempt): array
    {
        $response = $this->http()
            ->withToken($this->cred('api_key'))
            ->post($this->baseUrl() . '/checkouts', [
                'data' => [
                    'type' => 'checkouts',
                    'attributes' => [
                        'checkout_data' => [
                            'email' => $attempt->user->email ?? '',
                            'name' => $attempt->user->name ?? '',
                            'custom' => [
                                'attempt_token' => $attempt->token,
                                'order_total_usd' => (string) $attempt->total,
                            ],
                        ],
                        'test_mode' => $this->isTestMode(),
                    ],
                    'relationships' => [
                        'store' => ['data' => ['type' => 'stores', 'id' => (string) $this->cred('store_id')]],
                        'variant' => ['data' => ['type' => 'variants', 'id' => (string) $this->cred('variant_id')]],
                    ],
                ],
            ]);

        $data = $response->json('data') ?? [];
        $checkoutUrl = $data['attributes']['url'] ?? null;

        if (! $checkoutUrl || ! $response->successful()) {
            \Illuminate\Support\Facades\Log::error('Lemon Squeezy checkout create failed', ['response' => $response->json()]);
            throw new \RuntimeException('Lemon Squeezy is not available right now. Please try another payment method.');
        }

        $attempt->update(['gateway_reference' => $data['id'] ?? null]);

        return ['redirect' => $checkoutUrl];
    }

    public function verifyCallback(Request $request): array
    {
        // Lemon Squeezy confirms payment via webhook. If the customer lands
        // here after paying, the webhook may already have completed the order.
        $token = $request->query('ref');
        $attempt = $token ? \App\Models\PaymentAttempt::where('token', $token)->first() : null;

        if ($attempt && $attempt->status === 'completed') {
            return [
                'ok' => true,
                'attempt_token' => $token,
                'gateway_reference' => $attempt->gateway_reference,
                'amount' => null,
                'raw' => [],
            ];
        }

        return $this->fail('awaiting webhook confirmation', $request->all());
    }

    public function verifyWebhook(Request $request): array
    {
        $signature = $request->header('X-Signature', '');
        $secret = (string) $this->cred('signing_secret');

        if (! $secret || ! $signature || ! hash_equals(hash_hmac('sha256', $request->getContent(), $secret), $signature)) {
            return $this->fail('invalid webhook signature');
        }

        $payload = $request->json()->all();
        $event = $payload['meta']['event_name'] ?? '';

        if ($event !== 'order_created') {
            return ['ok' => false];
        }

        $attributes = $payload['data']['attributes'] ?? [];

        return [
            'ok' => true,
            'attempt_token' => $attributes['checkout_data']['custom']['attempt_token'] ?? null,
            'gateway_reference' => (string) ($payload['data']['id'] ?? ''),
            'amount' => isset($attributes['total']) ? ((float) $attributes['total'] / 100) : null,
            'raw' => $payload,
        ];
    }
}
