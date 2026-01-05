<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Coupon extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'type',
        'value',
        'min_order',
        'max_discount',
        'usage_limit',
        'used_count',
        'usage_per_user',
        'expires_at',
        'is_active',
        'description',
    ];

    protected $casts = [
        'value' => 'decimal:2',
        'min_order' => 'decimal:2',
        'max_discount' => 'decimal:2',
        'used_count' => 'integer',
        'usage_limit' => 'integer',
        'usage_per_user' => 'integer',
        'expires_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    public function usages(): HasMany
    {
        return $this->hasMany(CouponUsage::class);
    }

    /**
     * Check if coupon is valid
     */
    public function isValid($userId = null, $orderTotal = 0): array
    {
        // Check if active
        if (!$this->is_active) {
            return ['valid' => false, 'message' => 'This coupon is not active.'];
        }

        // Check expiry
        if ($this->expires_at && $this->expires_at->isPast()) {
            return ['valid' => false, 'message' => 'This coupon has expired.'];
        }

        // Check minimum order
        if ($orderTotal < $this->min_order) {
            return ['valid' => false, 'message' => "Minimum order amount is $" . number_format($this->min_order, 2) . '.'];
        }

        // Check total usage limit
        if ($this->usage_limit && $this->used_count >= $this->usage_limit) {
            return ['valid' => false, 'message' => 'This coupon has reached its usage limit.'];
        }

        // Check per-user usage limit
        if ($userId) {
            $userUsageCount = $this->usages()->where('user_id', $userId)->count();
            if ($userUsageCount >= $this->usage_per_user) {
                return ['valid' => false, 'message' => 'You have already used this coupon the maximum number of times.'];
            }
        }

        return ['valid' => true, 'message' => 'Coupon is valid.'];
    }

    /**
     * Calculate discount amount
     */
    public function calculateDiscount($orderTotal): float
    {
        if ($this->type === 'flat') {
            return min($this->value, $orderTotal); // Can't discount more than order total
        } else {
            // Percentage
            $discount = ($orderTotal * $this->value) / 100;
            if ($this->max_discount) {
                $discount = min($discount, $this->max_discount);
            }
            return min($discount, $orderTotal); // Can't discount more than order total
        }
    }

    /**
     * Scope for active coupons
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true)
            ->where(function ($q) {
                $q->whereNull('expires_at')
                  ->orWhere('expires_at', '>', now());
            });
    }
}
