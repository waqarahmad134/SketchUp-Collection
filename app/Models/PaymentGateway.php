<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentGateway extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'description',
        'is_enabled',
        'test_mode',
        'credentials',
        'sort_order',
    ];

    protected $casts = [
        'is_enabled' => 'boolean',
        'test_mode' => 'boolean',
    ];

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    /**
     * Credentials are stored encrypted. Access as array.
     */
    public function getCredentialsAttribute($value): array
    {
        if (empty($value)) {
            return [];
        }

        try {
            $decrypted = decrypt($value);
            return is_array($decrypted) ? $decrypted : [];
        } catch (\Throwable) {
            return [];
        }
    }

    public function setCredentialsAttribute($value): void
    {
        $this->attributes['credentials'] = encrypt($value ?? []);
    }

    /**
     * Field definitions for this gateway from config.
     */
    public function fields(): array
    {
        return config("payment-gateways.definitions.{$this->slug}.fields", []);
    }

    public function definition(): array
    {
        return config("payment-gateways.definitions.{$this->slug}", []);
    }

    /**
     * Build the driver instance for this gateway.
     */
    public function driver(): \App\Services\Payments\GatewayDriver
    {
        $driverClass = config("payment-gateways.definitions.{$this->slug}.driver");

        if (! $driverClass || ! class_exists($driverClass)) {
            throw new \RuntimeException("No payment driver configured for gateway [{$this->slug}].");
        }

        return new $driverClass($this);
    }

    public function scopeEnabled($query)
    {
        return $query->where('is_enabled', true)->orderBy('sort_order');
    }
}
