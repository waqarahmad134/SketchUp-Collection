<?php

namespace App\Services\Payments;

use App\Models\PaymentAttempt;
use Illuminate\Http\Request;

/**
 * JazzCash payment gateway (mobile wallet + cards, Pakistan).
 *
 * Uses the documented JazzCash v2 form-post flow: we build a signed request
 * (pp_SecureHash via HMAC-SHA256 over the sorted pp_ fields with the
 * Integrity Salt) and auto-post a form to JazzCash. JazzCash then POSTs the
 * result back to our Return URL with its own pp_SecureHash, which we verify.
 *
 * NOTE: test every change against JazzCash sandbox credentials before
 * enabling live mode. Field names follow JazzCash's published docs.
 */
class JazzCashDriver extends GatewayDriver
{
    public function endpoint(): string
    {
        return $this->isTestMode()
            ? 'https://sandbox.jazzcash.com.pk/Customer/api/v2/JazzCash/DoTransaction'
            : 'https://payments.jazzcash.com.pk/Customer/api/v2/JazzCash/DoTransaction';
    }

    public function initiate(PaymentAttempt $attempt): array
    {
        $amount = (int) round($this->chargeAmount($attempt) * 100); // paisa
        $now = now();
        $txnRef = 'TXN' . $now->format('YmdHis') . $attempt->id;

        $fields = [
            'pp_Version' => '2.0',
            'pp_TxnType' => 'MWALLET',
            'pp_Language' => 'EN',
            'pp_MerchantID' => $this->cred('merchant_id'),
            'pp_SubMerchantID' => '',
            'pp_Password' => $this->cred('password'),
            'pp_BankID' => '',
            'pp_ProductID' => '',
            'pp_TxnRefNo' => $txnRef,
            'pp_Amount' => (string) $amount,
            'pp_TxnCurrency' => 'PKR',
            'pp_TxnDateTime' => $now->format('YmdHis'),
            'pp_TxnExpiryDateTime' => $now->copy()->addHour()->format('YmdHis'),
            'pp_BillReference' => $attempt->token,
            'pp_Description' => 'SketchUp Collection Order ' . $attempt->token,
            'pp_ReturnURL' => $this->returnUrl(),
            'ppmpf_1' => $attempt->token,
        ];

        $fields['pp_SecureHash'] = $this->makeSecureHash($fields);

        $attempt->update(['gateway_reference' => $txnRef]);

        return ['form' => ['action' => $this->endpoint(), 'fields' => $fields]];
    }

    public function verifyCallback(Request $request): array
    {
        $data = $request->all();
        $raw = $data;

        $receivedHash = $data['pp_SecureHash'] ?? null;
        if (! $receivedHash || $receivedHash !== $this->makeSecureHash($data)) {
            return $this->fail('invalid secure hash', $raw);
        }

        // JazzCash response codes: 000 = success
        $responseCode = $data['pp_ResponseCode'] ?? '';
        if ($responseCode !== '000') {
            return ['ok' => false, 'attempt_token' => $data['ppmpf_1'] ?? null,
                'gateway_reference' => $data['pp_TxnRefNo'] ?? null,
                'amount' => isset($data['pp_Amount']) ? ((float) $data['pp_Amount'] / 100) : null,
                'raw' => $raw];
        }

        return [
            'ok' => true,
            'attempt_token' => $data['ppmpf_1'] ?? $data['pp_BillReference'] ?? null,
            'gateway_reference' => $data['pp_TxnRefNo'] ?? null,
            'amount' => isset($data['pp_Amount']) ? ((float) $data['pp_Amount'] / 100) : null,
            'raw' => $raw,
        ];
    }

    /**
     * JazzCash secure hash: sort pp_ fields, join values with '&',
     * then HMAC-SHA256 with the Integrity Salt.
     */
    protected function makeSecureHash(array $fields): string
    {
        $salt = (string) $this->cred('integrity_salt');

        $values = [];
        foreach ($fields as $key => $value) {
            if ($key === 'pp_SecureHash') {
                continue;
            }
            if (! str_starts_with($key, 'pp_')) {
                continue;
            }
            $values[$key] = $value;
        }
        ksort($values);

        $payload = $salt . '&' . implode('&', array_values($values));

        return hash_hmac('sha256', $payload, $salt);
    }
}
