<?php

namespace App\Http\Controllers;

use App\Models\PointsTransaction;
use App\Models\Product;
use App\Models\Referral;
use App\Models\User;
use App\Models\Order;
use App\Models\Transaction;
use App\Models\Review;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;

class CreatorController extends Controller
{
    public function show(User $user): View
    {
        $isOwnProfile = Auth::check() && Auth::id() === $user->id;
        $isSeller = $user->canSell();
        
        // Get products (only active for public view, all for own profile)
        $productsQuery = Product::where('user_id', $user->id);
        if (!$isOwnProfile) {
            $productsQuery->where('is_active', true);
        }
        $products = $productsQuery->orderBy('sort_order')->get();

        $avatar = $user->avatar ?? 'https://ui-avatars.com/api/?name=' . urlencode($user->name) . '&background=22b5ff&color=fff';

        // Get orders (only for own profile)
        $orders = $isOwnProfile ? Order::where('user_id', $user->id)
            ->with(['items.product'])
            ->latest()
            ->paginate(10) : null;

        // Get payment transactions (only for own profile)
        $transactions = $isOwnProfile ? Transaction::where('user_id', $user->id)
            ->with(['order'])
            ->latest()
            ->paginate(10) : null;

        // Get points transactions (only for own profile)
        $pointsTransactions = $isOwnProfile ? PointsTransaction::where('user_id', $user->id)
            ->latest()
            ->paginate(10) : null;

        // Get reviews (reviews given and received)
        $reviewsGiven = $isOwnProfile ? Review::where('user_id', $user->id)
            ->with(['product'])
            ->latest()
            ->get() : collect();
        
        $reviewsReceived = $isSeller ? Review::whereHas('product', function($q) use ($user) {
                $q->where('user_id', $user->id);
            })
            ->with(['user', 'product'])
            ->latest()
            ->get() : collect();

        // Get payment gateways used (only for own profile)
        $paymentGateways = $isOwnProfile ? Transaction::where('user_id', $user->id)
            ->whereNotNull('payment_gateway')
            ->distinct()
            ->pluck('payment_gateway')
            ->filter()
            ->values() : collect();

        // Get referral data (only for own profile)
        $referrals = $isOwnProfile ? Referral::where('referrer_id', $user->id)
            ->with(['referred'])
            ->latest()
            ->paginate(10) : null;

        $referralStats = $isOwnProfile ? [
            'total_referrals' => $user->referral_count ?? 0,
            'total_earnings' => $user->referral_earnings ?? 0,
            'pending_referrals' => Referral::where('referrer_id', $user->id)->where('status', 'pending')->count(),
            'completed_referrals' => Referral::where('referrer_id', $user->id)->where('status', 'completed')->count(),
            'rewarded_referrals' => Referral::where('referrer_id', $user->id)->where('status', 'rewarded')->count(),
        ] : null;

        return view('creators.show', [
            'title' => $user->name . ($isOwnProfile ? ' - My Profile' : ' - Creator Profile'),
            'metaDescription' => $isOwnProfile ? 'Manage your profile, orders, and settings' : 'View products uploaded by ' . $user->name,
            'user' => $user,
            'avatar' => $avatar,
            'products' => $products,
            'orders' => $orders,
            'transactions' => $transactions,
            'pointsTransactions' => $pointsTransactions,
            'reviewsGiven' => $reviewsGiven,
            'reviewsReceived' => $reviewsReceived,
            'paymentGateways' => $paymentGateways,
            'referrals' => $referrals,
            'referralStats' => $referralStats,
            'isOwnProfile' => $isOwnProfile,
            'isSeller' => $isSeller,
        ]);
    }

    /**
     * Save or update the payment method metadata for the authenticated user.
     * We only store safe metadata (brand, last4, expiry). Full card/CVC is not persisted.
     */
    public function updatePaymentMethod(Request $request): RedirectResponse
    {
        $user = $request->user();

        $validator = Validator::make($request->all(), [
            'card_number' => ['required', 'string', 'regex:/^[0-9\\s-]{12,19}$/'],
            'card_exp_month' => ['nullable', 'integer', 'min:1', 'max:12'],
            'card_exp_year' => ['nullable', 'integer', 'min:' . date('Y'), 'max:' . (date('Y') + 15)],
            'card_cvc' => ['required', 'digits_between:3,4'],
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $data = $validator->validated();

        // Normalize card number (do not store full PAN)
        $normalized = preg_replace('/[^0-9]/', '', $data['card_number'] ?? '');
        $last4 = substr($normalized, -4);
        $brand = $this->detectCardBrand($normalized);

        $user->update([
            'stripe_customer_id' => $user->stripe_customer_id ?: null,
            'stripe_payment_method_id' => null,
            'card_brand' => $brand,
            'card_last4' => $last4,
            'card_exp_month' => $data['card_exp_month'] ?? null,
            'card_exp_year' => $data['card_exp_year'] ?? null,
        ]);

        return back()->with('status', 'Payment method updated.');
    }

    /**
     * Lightweight card brand detection based on BIN ranges.
     */
    private function detectCardBrand(string $number): ?string
    {
        if (preg_match('/^4[0-9]{5}/', $number)) {
            return 'visa';
        }

        if (preg_match('/^(5[1-5][0-9]{4}|2(2[2-9][0-9]{3}|[3-6][0-9]{4}|7[01][0-9]{3}|720[0-9]{2}))/', $number)) {
            return 'mastercard';
        }

        if (preg_match('/^3[47][0-9]{4}/', $number)) {
            return 'amex';
        }

        if (preg_match('/^(6011|65[0-9]{2}|64[4-9][0-9])/', $number)) {
            return 'discover';
        }

        if (preg_match('/^35(2[89]|[3-8][0-9])/', $number)) {
            return 'jcb';
        }

        if (preg_match('/^(30[0-5]|36|38|39)/', $number)) {
            return 'diners';
        }

        return 'card';
    }
}

