<?php

namespace App\Support;

use App\Models\Setting;

/**
 * Display-only currency conversion. Prices are stored and charged in USD;
 * this helper converts for display based on the visitor's session currency.
 */
class Currency
{
    public static function current(): string
    {
        $currency = session('currency', 'USD');

        return in_array($currency, ['USD', 'PKR']) ? $currency : 'USD';
    }

    public static function rate(): float
    {
        return (float) Setting::get('usd_to_pkr_rate', config('payment-gateways.usd_to_pkr_rate', 278));
    }

    public static function convert(float $usd): float
    {
        return self::current() === 'PKR' ? $usd * self::rate() : $usd;
    }

    public static function format(float $usd, bool $withSymbol = true): string
    {
        $amount = self::convert($usd);

        if (self::current() === 'PKR') {
            $formatted = 'Rs ' . number_format(round($amount));
        } else {
            $formatted = '$' . number_format($amount, 2);
        }

        return $withSymbol ? $formatted : ltrim($formatted, '$');
    }

    public static function symbol(): string
    {
        return self::current() === 'PKR' ? 'Rs' : '$';
    }
}
