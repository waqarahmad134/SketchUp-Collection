<?php

namespace App\Services\Payments;

use App\Models\PaymentAttempt;
use App\Models\PaymentGateway;
use App\Models\Setting;
use Illuminate\Http\Request;

/**
 * Base class for payment gateway drivers.
 *
 * Flow:
 *  1. initiate($attempt) -> ['redirect' => url] or ['form' => ['action' => url, 'fields' => [...]]]
 *  2. Customer pays at the provider.
 *  3. Provider calls back to payment.callback (browser return) or
 *     payment.webhook (server-to-server).
 *  4. verifyCallback()/verifyWebhook() return a result array:
 *     ['ok' => bool, 'attempt_token' => ?string, 'gateway_reference' => ?string,
 *      'amount' => ?float, 'raw' => array]
 */
abstract class GatewayDriver
{
    public function __construct(protected PaymentGateway $gateway) {}

    abstract public function initiate(PaymentAttempt $attempt): array;

    abstract public function verifyCallback(Request $request): array;

    public function verifyWebhook(Request $request): array
    {
        return ['ok' => false];
    }

    protected function cred(string $key, mixed $default = null): mixed
    {
        $creds = $this->gateway->credentials ?? [];
        return $creds[$key] ?? $default;
    }

    public function isTestMode(): bool
    {
        return (bool) $this->gateway->test_mode;
    }

    public function returnUrl(): string
    {
        return route('payment.callback', ['gateway' => $this->gateway->slug]);
    }

    public function webhookUrl(): string
    {
        return route('payment.webhook', ['gateway' => $this->gateway->slug]);
    }

    public function chargeCurrency(): string
    {
        return config("payment-gateways.definitions.{$this->gateway->slug}.currency", 'USD');
    }

    /**
     * Amount to charge at the provider, converted to the gateway currency.
     */
    public function chargeAmount(PaymentAttempt $attempt): float
    {
        $total = (float) $attempt->total;

        if ($this->chargeCurrency() === 'PKR') {
            $rate = (float) Setting::get('usd_to_pkr_rate', config('payment-gateways.usd_to_pkr_rate', 278));
            return round($total * $rate, 2);
        }

        return round($total, 2);
    }

    protected function http(): \Illuminate\Http\Client\PendingRequest
    {
        return \Illuminate\Support\Facades\Http::timeout(30)->acceptJson();
    }

    protected function fail(string $reason, array $raw = []): array
    {
        \Illuminate\Support\Facades\Log::warning('Payment callback verification failed', [
            'gateway' => $this->gateway->slug,
            'reason' => $reason,
        ]);

        return ['ok' => false, 'attempt_token' => null, 'gateway_reference' => null, 'amount' => null, 'raw' => $raw];
    }
}
