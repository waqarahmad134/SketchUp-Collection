<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PointsTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'points',
        'type',
        'description',
        'related_id',
        'related_type',
        'balance_after',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopeEarned($query)
    {
        return $query->where('points', '>', 0);
    }

    public function scopeSpent($query)
    {
        return $query->where('points', '<', 0);
    }

    public function scopeByType($query, string $type)
    {
        return $query->where('type', $type);
    }
}
