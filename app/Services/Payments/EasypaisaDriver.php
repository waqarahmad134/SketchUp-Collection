<?php

namespace App\Services\Payments;

use App\Models\PaymentAttempt;
use Illuminate\Http\Request;

/**
 * Easypaisa payment gateway (mobile wallet, Pakistan).
 *
 * Uses the documented Easypaisa merchant flow: we create a server-to-server
 * transaction-initiation request signed with the Hash Key, then redirect the
 * customer to the Easypaisa checkout URL. Easypaisa POSTs the result back to
 * our Return URL with a hash we verify.
 *
 * NOTE: test every change against Easypaisa sandbox credentials before
 * enabling live mode. Field names follow Easypaisa's published docs.
 */
class EasypaisaDriver extends GatewayDriver
{
    protected function baseUrl(): string
    {
        return $this->isTestMode()
            ? 'https://easypaystg.easypaisa.com.pk'
            : 'https://easypay.easypaisa.com.pk';
    }

    public function initiate(PaymentAttempt $attempt): array
    {
        $amount = $this->chargeAmount($attempt);
        $orderId = 'EP' . now()->format('YmdHis') . $attempt->id;

        $payload = [
            'storeId' => $this->cred('store_id'),
            'orderId' => $orderId,
            'transactionAmount' => number_format($amount, 2, '.', ''),
            'transactionType' => 'MA',
            'mobileAccountNo' => '',
            'emailAddress' => $attempt->user->email ?? '',
        ];
        $payload['hash'] = $this->makeHash($payload);

        $response = $this->http()
            ->withBasicAuth($this->cred('username'), $this->cred('password'))
            ->post($this->baseUrl() . '/easypay-service/rest/v4/initiateTransaction', $payload);

        $data = $response->json() ?? [];

        $paymentUrl = $data['paymentUrl'] ?? $data['redirectUrl'] ?? null;
        if (! $paymentUrl || ! $response->successful()) {
            \Illuminate\Support\Facades\Log::error('Easypaisa initiate failed', ['response' => $data]);
            throw new \RuntimeException('Easypaisa is not available right now. Please try another payment method.');
        }

        $attempt->update(['gateway_reference' => $orderId]);

        // Carry our attempt token so the callback can find the order.
        $separator = str_contains($paymentUrl, '?') ? '&' : '?';

        return ['redirect' => $paymentUrl . $separator . 'ref=' . $attempt->token];
    }

    public function verifyCallback(Request $request): array
    {
        $data = $request->all();
        $raw = $data;

        $receivedHash = $data['hash'] ?? null;
        if (! $receivedHash || ! hash_equals($this->makeHash($data), $receivedHash)) {
            return $this->fail('invalid hash', $raw);
        }

        $status = strtolower($data['transactionStatus'] ?? $data['status'] ?? '');
        if (! in_array($status, ['success', 'completed', 'paid', '000'])) {
            return ['ok' => false, 'attempt_token' => $request->query('ref'),
                'gateway_reference' => $data['orderId'] ?? null, 'amount' => null, 'raw' => $raw];
        }

        return [
            'ok' => true,
            'attempt_token' => $request->query('ref'),
            'gateway_reference' => $data['orderId'] ?? null,
            'amount' => isset($data['transactionAmount']) ? (float) $data['transactionAmount'] : null,
            'raw' => $raw,
        ];
    }

    protected function makeHash(array $fields): string
    {
        $key = (string) $this->cred('hash_key');

        $values = [];
        foreach ($fields as $k => $v) {
            if (in_array($k, ['hash', 'signature'], true)) {
                continue;
            }
            $values[$k] = $v;
        }
        ksort($values);

        return hash_hmac('sha256', implode('|', array_values($values)), $key);
    }
}
