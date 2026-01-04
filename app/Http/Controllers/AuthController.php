<?php

namespace App\Http\Controllers;

use App\Models\DailyLoginBonus;
use App\Models\PointsTransaction;
use App\Models\Referral;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function showLogin(Request $request): View
    {
        $claimDailyBonus = $request->query('claim_daily_bonus');
        
        return view('auth.login', [
            'title' => 'Login - SketchUp Collection',
            'metaDescription' => 'Sign in to access your purchased assets and manage your profile.',
            'claimDailyBonus' => $claimDailyBonus,
        ]);
    }

    public function showRegister(Request $request): View
    {
        $referralCode = $request->query('ref');
        $claimDailyBonus = $request->query('claim_daily_bonus');
        $referrer = null;
        
        if ($referralCode) {
            $referrer = User::where('referral_code', $referralCode)->first();
        }

        return view('auth.register', [
            'title' => 'Sign Up - SketchUp Collection',
            'metaDescription' => 'Create a free SketchUp Collection account to access assets and bundles.',
            'referralCode' => $referralCode,
            'referrer' => $referrer,
            'claimDailyBonus' => $claimDailyBonus,
        ]);
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            
            // Check if user came from daily bonus claim (from query or form)
            if ($request->query('claim_daily_bonus') || $request->input('claim_daily_bonus')) {
                $request->session()->put('claim_daily_bonus_after_login', true);
            }
            
            // Auto-claim daily bonus if pending
            $this->processPendingDailyBonus($request);
            
            return redirect()->intended(route('home'))->with('status', 'Logged in successfully.');
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    public function register(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', 'min:8'],
            'referral_code' => ['nullable', 'string', 'max:20'],
        ]);

        DB::beginTransaction();
        
        try {
            // Find referrer if referral code provided
            $referrer = null;
            $referralCode = !empty($data['referral_code']) ? strtoupper(trim($data['referral_code'])) : null;
            
            if ($referralCode) {
                $referrer = User::where('referral_code', $referralCode)->first();
                
                if (!$referrer) {
                    DB::rollBack();
                    return back()->withErrors([
                        'referral_code' => 'The referral code you entered is invalid. Please check and try again.',
                    ])->withInput();
                }
                
                // Prevent self-referral
                if ($referrer->email === $data['email']) {
                    DB::rollBack();
                    return back()->withErrors([
                        'referral_code' => 'You cannot use your own referral code.',
                    ])->withInput();
                }
            }

            // Create user
            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
                'referred_by' => $referrer?->id,
            ]);

            // Generate referral code for new user
            $user->generateReferralCode();

            // Award signup bonus (default 500 coins, configurable in admin)
            $signupBonus = (int) Setting::get('signup_bonus_points', 500);
            if ($signupBonus > 0) {
                $user->addPoints($signupBonus, 'signup', 'Welcome bonus for signing up');
            }

            // Create referral record if referred
            if ($referrer) {
                Referral::create([
                    'referrer_id' => $referrer->id,
                    'referred_id' => $user->id,
                    'status' => 'pending',
                    'reward_type' => null, // Will be set when reward is actually given (e.g., 'purchase')
                ]);

                // Increment referrer's referral count
                $referrer->incrementReferralCount();
            }

            DB::commit();

            Auth::login($user);

            // Check if user came from daily bonus claim (from query or form)
            if ($request->query('claim_daily_bonus') || $request->input('claim_daily_bonus')) {
                $request->session()->put('claim_daily_bonus_after_login', true);
            }
            
            // Auto-claim daily bonus if pending
            $this->processPendingDailyBonus($request);

            $message = 'Account created successfully.';
            if ($referrer) {
                $message .= ' You were referred by ' . $referrer->name . '!';
            }

            return redirect()->intended(route('home'))->with('status', $message);
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Registration failed. Please try again.'])->withInput();
        }
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home')->with('status', 'Logged out successfully.');
    }

    /**
     * Process pending daily bonus claim after login/registration
     */
    private function processPendingDailyBonus(Request $request): void
    {
        if (!$request->session()->get('claim_daily_bonus_after_login')) {
            return;
        }

        $user = $request->user();
        if (!$user) {
            return;
        }

        // Check if daily login is enabled
        $enabled = Setting::get('daily_login_enabled', '1') === '1' || Setting::get('daily_login_enabled', '1') === true;
        if (!$enabled) {
            $request->session()->forget('claim_daily_bonus_after_login');
            return;
        }

        // Check if user can claim today
        if (!DailyLoginBonus::canClaimToday($user)) {
            $request->session()->forget('claim_daily_bonus_after_login');
            return;
        }

        try {
            DB::beginTransaction();
            
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
            $pointsToAward = min($pointsForToday, $remainingWeekly);
            
            if ($pointsToAward > 0) {
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
                
                $request->session()->put('daily_bonus_claimed', $pointsToAward);
            }
            
            DB::commit();
            $request->session()->forget('claim_daily_bonus_after_login');
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Auto-claim daily bonus failed: ' . $e->getMessage());
        }
    }
}

