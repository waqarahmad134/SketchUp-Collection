<?php

namespace App\Services\Payments;

use App\Models\PaymentAttempt;
use Illuminate\Http\Request;

/**
 * PayPro payment gateway (hosted checkout: wallets, cards, bank transfer).
 *
 * Flow: we create an order via PayPro's API, receive a hosted payment URL,
 * redirect the customer there, and PayPro redirects back to our Return URL
 * with the payment status, which we verify server-to-server.
 *
 * NOTE: test every change against PayPro sandbox credentials before
 * enabling live mode. Field names follow PayPro's published docs.
 */
class PayProDriver extends GatewayDriver
{
    protected function baseUrl(): string
    {
        return $this->isTestMode()
            ? 'https://api-stg.paypro.com.pk/v2'
            : 'https://api.paypro.com.pk/v2';
    }

    public function initiate(PaymentAttempt $attempt): array
    {
        $amount = $this->chargeAmount($attempt);
        $orderNumber = 'PP' . now()->format('YmdHis') . $attempt->id;

        $response = $this->http()
            ->withHeaders([
                'X-Merchant-Id' => $this->cred('merchant_id'),
                'X-Merchant-Password' => $this->cred('merchant_password'),
            ])
            ->post($this->baseUrl() . '/ppro/co', [
                'merchantId' => $this->cred('merchant_id'),
                'orderNumber' => $orderNumber,
                'orderAmount' => (int) round($amount * 100), // paisa
                'orderCurrency' => 'PKR',
                'orderDueDate' => now()->addDay()->toIso8601String(),
                'customerName' => $attempt->user->name ?? '',
                'customerEmail' => $attempt->user->email ?? '',
                'callbackUrl' => $this->returnUrl() . '?ref=' . $attempt->token,
            ]);

        $data = $response->json() ?? [];
        $paymentUrl = $data['paymentUrl'] ?? $data['url'] ?? null;

        if (! $paymentUrl || ! $response->successful()) {
            \Illuminate\Support\Facades\Log::error('PayPro initiate failed', ['response' => $data]);
            throw new \RuntimeException('PayPro is not available right now. Please try another payment method.');
        }

        $attempt->update(['gateway_reference' => $orderNumber]);

        return ['redirect' => $paymentUrl];
    }

    public function verifyCallback(Request $request): array
    {
        $data = $request->all();
        $raw = $data;

        $attemptToken = $request->query('ref') ?? $data['ref'] ?? null;
        $status = strtolower($data['status'] ?? $data['paymentStatus'] ?? '');

        if (! in_array($status, ['success', 'completed', 'paid', 'approved'])) {
            return ['ok' => false, 'attempt_token' => $attemptToken,
                'gateway_reference' => $data['orderNumber'] ?? null, 'amount' => null, 'raw' => $raw];
        }

        // Server-to-server verification of the order status.
        $orderNumber = $data['orderNumber'] ?? null;
        if ($orderNumber && ! $this->verifyOrderStatus($orderNumber)) {
            return $this->fail('server verification failed', $raw);
        }

        return [
            'ok' => true,
            'attempt_token' => $attemptToken,
            'gateway_reference' => $orderNumber,
            'amount' => isset($data['orderAmount']) ? ((float) $data['orderAmount'] / 100) : null,
            'raw' => $raw,
        ];
    }

    protected function verifyOrderStatus(string $orderNumber): bool
    {
        try {
            $response = $this->http()
                ->withHeaders([
                    'X-Merchant-Id' => $this->cred('merchant_id'),
                    'X-Merchant-Password' => $this->cred('merchant_password'),
                ])
                ->get($this->baseUrl() . '/ppro/co/' . $orderNumber);

            $data = $response->json() ?? [];
            $status = strtolower($data['status'] ?? '');

            return in_array($status, ['success', 'completed', 'paid', 'approved']);
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('PayPro status check failed', ['error' => $e->getMessage()]);
            return false;
        }
    }
}
