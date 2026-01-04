<?php

namespace App\Http\Controllers;

use App\Models\DailyLoginBonus;
use App\Models\PointsTransaction;
use App\Models\Setting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DailyLoginController extends Controller
{
    /**
     * Check if user can claim daily bonus and get current status
     */
    public function status(Request $request): JsonResponse
    {
        $user = $request->user();
        
        // Check if daily login is enabled
        $enabled = Setting::get('daily_login_enabled', '1') === '1' || Setting::get('daily_login_enabled', '1') === true;
        if (!$enabled) {
            return response()->json(['can_claim' => false, 'enabled' => false]);
        }

        // If not logged in, show popup but can't claim
        if (!$user) {
            return response()->json([
                'can_claim' => false,
                'enabled' => true,
                'requires_login' => true,
            ]);
        }

        $canClaim = DailyLoginBonus::canClaimToday($user);
        $streakDay = DailyLoginBonus::getCurrentStreakDay($user);
        $maxWeeklyCoins = (int) Setting::get('daily_login_max_weekly_coins', 2000);
        
        // Calculate points for current day (distribute weekly max across 7 days)
        // Day 1: 10%, Day 2: 12%, Day 3: 14%, Day 4: 16%, Day 5: 18%, Day 6: 15%, Day 7: 15%
        $dayPercentages = [0.10, 0.12, 0.14, 0.16, 0.18, 0.15, 0.15];
        $dayIndex = min($streakDay - 1, 6);
        $pointsForToday = (int) round($maxWeeklyCoins * $dayPercentages[$dayIndex]);
        
        // Get current week's total claimed
        $weekStart = DailyLoginBonus::getWeekStartDate();
        $weekTotal = DailyLoginBonus::where('user_id', $user->id)
            ->where('week_start_date', $weekStart->format('Y-m-d'))
            ->sum('points_awarded');
        
        $remainingWeekly = max(0, $maxWeeklyCoins - $weekTotal);
        
        return response()->json([
            'can_claim' => $canClaim,
            'streak_day' => $streakDay,
            'points_for_today' => $pointsForToday,
            'week_total' => $weekTotal,
            'max_weekly_coins' => $maxWeeklyCoins,
            'remaining_weekly' => $remainingWeekly,
            'requires_login' => false,
        ]);
    }

    /**
     * Claim daily login bonus
     */
    public function claim(Request $request): JsonResponse
    {
        $user = $request->user();
        
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'You must be logged in to claim daily bonus.',
            ], 401);
        }

        // Check if daily login is enabled
        $enabled = Setting::get('daily_login_enabled', '1') === '1' || Setting::get('daily_login_enabled', '1') === true;
        if (!$enabled) {
            return response()->json([
                'success' => false,
                'message' => 'Daily login bonus is currently disabled.',
            ], 400);
        }

        if (!DailyLoginBonus::canClaimToday($user)) {
            return response()->json([
                'success' => false,
                'message' => 'You have already claimed your daily bonus today.',
            ], 400);
        }

        DB::beginTransaction();
        
        try {
            $today = new \DateTime();
            $streakDay = DailyLoginBonus::getCurrentStreakDay($user);
            $weekStart = DailyLoginBonus::getWeekStartDate($today);
            $maxWeeklyCoins = (int) Setting::get('daily_login_max_weekly_coins', 2000);
            
            // Calculate points for current day
            $dayPercentages = [0.10, 0.12, 0.14, 0.16, 0.18, 0.15, 0.15];
            $dayIndex = min($streakDay - 1, 6);
            $pointsForToday = (int) round($maxWeeklyCoins * $dayPercentages[$dayIndex]);
            
            // Check weekly limit
            $weekTotal = DailyLoginBonus::where('user_id', $user->id)
                ->where('week_start_date', $weekStart->format('Y-m-d'))
                ->sum('points_awarded');
            
            $remainingWeekly = max(0, $maxWeeklyCoins - $weekTotal);
            
            if ($remainingWeekly <= 0) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'You have reached the weekly limit for daily login bonuses.',
                ], 400);
            }
            
            // Adjust points if it would exceed weekly limit
            $pointsToAward = min($pointsForToday, $remainingWeekly);
            
            if ($pointsToAward <= 0) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'No points available to claim.',
                ], 400);
            }
            
            // Create daily login bonus record
            DailyLoginBonus::create([
                'user_id' => $user->id,
                'claimed_date' => $today->format('Y-m-d'),
                'streak_day' => $streakDay,
                'week_start_date' => $weekStart->format('Y-m-d'),
                'points_awarded' => $pointsToAward,
            ]);
            
            // Award points to user
            $user->addPoints(
                $pointsToAward,
                'daily_login',
                "Daily login bonus - Day {$streakDay}",
                null,
                null
            );
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => "You've earned {$pointsToAward} SKP coins!",
                'points_awarded' => $pointsToAward,
                'streak_day' => $streakDay,
                'new_balance' => $user->fresh()->getPoints(),
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Daily login bonus claim failed: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to claim daily bonus. Please try again.',
            ], 500);
        }
    }
}
