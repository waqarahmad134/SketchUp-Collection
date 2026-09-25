<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class PaymentAttempt extends Model
{
    protected $fillable = [
        'token',
        'gateway_slug',
        'user_id',
        'cart',
        'subtotal',
        'tax',
        'discount',
        'total',
        'currency',
        'coupon_code',
        'coins_to_use',
        'status',
        'gateway_reference',
        'completed_at',
    ];

    protected $casts = [
        'cart' => 'array',
        'completed_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function (PaymentAttempt $attempt) {
            if (empty($attempt->token)) {
                $attempt->token = Str::random(48);
            }
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function gateway()
    {
        return $this->belongsTo(PaymentGateway::class, 'gateway_slug', 'slug');
    }

    public function markCompleted(string $gatewayReference): void
    {
        $this->update([
            'status' => 'completed',
            'gateway_reference' => $gatewayReference,
            'completed_at' => now(),
        ]);
    }
}
