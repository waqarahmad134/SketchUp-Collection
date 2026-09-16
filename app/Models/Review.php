<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'user_id',
        'name',
        'email',
        'rating',
        'comment',
        'status',
    ];

    protected $casts = [
        'rating' => 'integer',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * True when the reviewer actually bought this product
     * (completed order containing it). Shown as a "Verified purchase" badge.
     */
    public function getIsVerifiedAttribute(): bool
    {
        if (! $this->user_id) {
            return false;
        }

        return Order::where('user_id', $this->user_id)
            ->where('status', 'completed')
            ->whereHas('items', fn ($q) => $q->where('product_id', $this->product_id))
            ->exists();
    }
}

