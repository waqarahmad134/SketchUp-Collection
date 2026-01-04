<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DailyLoginBonus extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'claimed_date',
        'streak_day',
        'week_start_date',
        'points_awarded',
    ];

    protected $casts = [
        'claimed_date' => 'date',
        'week_start_date' => 'date',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public static function getWeekStartDate(\DateTime $date = null): \DateTime
    {
        $date = $date ?? new \DateTime();
        $dayOfWeek = (int) $date->format('w'); // 0 (Sunday) to 6 (Saturday)
        $mondayOffset = $dayOfWeek === 0 ? 6 : $dayOfWeek - 1; // Convert to Monday = 0
        
        $monday = clone $date;
        $monday->modify("-{$mondayOffset} days");
        $monday->setTime(0, 0, 0);
        
        return $monday;
    }

    public static function getCurrentStreakDay(User $user, \DateTime $date = null): int
    {
        $date = $date ?? new \DateTime();
        $weekStart = self::getWeekStartDate($date);
        
        // Get last claim in current week
        $lastClaim = self::where('user_id', $user->id)
            ->where('week_start_date', $weekStart->format('Y-m-d'))
            ->orderBy('claimed_date', 'desc')
            ->first();
        
        if (!$lastClaim) {
            return 1; // First day of the week
        }
        
        $lastClaimDate = new \DateTime($lastClaim->claimed_date);
        $daysDiff = $date->diff($lastClaimDate)->days;
        
        // If claimed today, return same day
        if ($daysDiff === 0) {
            return $lastClaim->streak_day;
        }
        
        // If claimed yesterday, continue streak
        if ($daysDiff === 1) {
            return min($lastClaim->streak_day + 1, 7);
        }
        
        // Streak broken, start from day 1
        return 1;
    }

    public static function canClaimToday(User $user): bool
    {
        $today = new \DateTime();
        $todayStr = $today->format('Y-m-d');
        
        return !self::where('user_id', $user->id)
            ->where('claimed_date', $todayStr)
            ->exists();
    }
}
