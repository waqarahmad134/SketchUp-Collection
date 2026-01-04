<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Referral extends Model
{
    use HasFactory;

    protected $fillable = [
        'referrer_id',
        'referred_id',
        'status',
        'reward_amount',
        'reward_type',
        'notes',
        'completed_at',
    ];

    protected $casts = [
        'reward_amount' => 'decimal:2',
        'completed_at' => 'datetime',
    ];

    public function referrer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'referrer_id');
    }

    public function referred(): BelongsTo
    {
        return $this->belongsTo(User::class, 'referred_id');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeRewarded($query)
    {
        return $query->where('status', 'rewarded');
    }

    public function markAsCompleted(): void
    {
        $this->update([
            'status' => 'completed',
            'completed_at' => now(),
        ]);
    }

    public function markAsRewarded(float $amount, string $type = 'purchase'): void
    {
        $this->update([
            'status' => 'rewarded',
            'reward_amount' => $amount,
            'reward_type' => $type,
            'completed_at' => now(),
        ]);
    }
}
