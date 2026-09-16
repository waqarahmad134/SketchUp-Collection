<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Coupon;
use App\Models\CouponUsage;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\PaymentAttempt;
use App\Models\PointsTransaction;
use App\Models\Referral;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminAnalyticsController extends Controller
{
    /**
     * Analytics overview: sales trends, funnel, and coupon / referral /
     * points aggregation in one place.
     */
    public function index(Request $request)
    {
        $range = (int) $request->get('range', 30);
        $range = in_array($range, [7, 30, 90]) ? $range : 30;
        $since = now()->subDays($range - 1)->startOfDay();

        // ---- KPIs (completed orders) ----
        $completed = Order::where('status', 'completed')->where('created_at', '>=', $since);
        $revenue = (clone $completed)->sum('total');
        $ordersCount = (clone $completed)->count();
        $aov = $ordersCount > 0 ? $revenue / $ordersCount : 0;
        $uniqueBuyers = Order::where('status', 'completed')
            ->where('created_at', '>=', $since)
            ->distinct('user_id')->count('user_id');

        // ---- Conversion funnel: payment attempts -> completed orders ----
        $attempts = PaymentAttempt::where('created_at', '>=', $since)->count();
        $conversionRate = $attempts > 0 ? ($ordersCount / $attempts) * 100 : 0;

        $funnelByGateway = PaymentAttempt::where('created_at', '>=', $since)
            ->select('gateway_slug', DB::raw('COUNT(*) as attempts'))
            ->groupBy('gateway_slug')
            ->get()
            ->keyBy('gateway_slug');

        $completedByGateway = Order::where('status', 'completed')
            ->where('created_at', '>=', $since)
            ->select('payment_method', DB::raw('COUNT(*) as orders'), DB::raw('SUM(total) as revenue'))
            ->groupBy('payment_method')
            ->get();

        $gateways = [];
        foreach ($completedByGateway as $row) {
            $gateways[] = [
                'gateway' => $row->payment_method ?: 'unknown',
                'attempts' => (int) ($funnelByGateway[$row->payment_method]->attempts ?? 0),
                'orders' => (int) $row->orders,
                'revenue' => (float) $row->revenue,
            ];
        }
        // Gateways with attempts but zero completions still show up.
        foreach ($funnelByGateway as $slug => $row) {
            if (!collect($gateways)->contains('gateway', $slug)) {
                $gateways[] = ['gateway' => $slug, 'attempts' => (int) $row->attempts, 'orders' => 0, 'revenue' => 0];
            }
        }
        usort($gateways, fn ($a, $b) => $b['revenue'] <=> $a['revenue']);

        // ---- Sales trend per day ----
        $daily = Order::where('status', 'completed')
            ->where('created_at', '>=', $since)
            ->select(DB::raw('DATE(created_at) as day'), DB::raw('SUM(total) as revenue'), DB::raw('COUNT(*) as orders'))
            ->groupBy('day')
            ->orderBy('day')
            ->get()
            ->keyBy('day');

        $trend = [];
        $maxRevenue = 0;
        for ($i = $range - 1; $i >= 0; $i--) {
            $day = now()->subDays($i)->toDateString();
            $rev = (float) ($daily[$day]->revenue ?? 0);
            $maxRevenue = max($maxRevenue, $rev);
            $trend[] = [
                'label' => now()->subDays($i)->format('M d'),
                'revenue' => $rev,
                'orders' => (int) ($daily[$day]->orders ?? 0),
            ];
        }

        // ---- Top products ----
        $topProducts = OrderItem::select('product_id', 'product_name', DB::raw('SUM(quantity) as sold'), DB::raw('SUM(total) as revenue'))
            ->whereHas('order', fn ($q) => $q->where('status', 'completed')->where('created_at', '>=', $since))
            ->groupBy('product_id', 'product_name')
            ->orderByDesc('revenue')
            ->limit(8)
            ->get();

        // ---- Coupon performance ----
        $couponUsages = CouponUsage::where('created_at', '>=', $since)
            ->select('coupon_id', DB::raw('COUNT(*) as uses'), DB::raw('SUM(discount_amount) as discount_given'))
            ->groupBy('coupon_id')
            ->with('coupon')
            ->orderByDesc('uses')
            ->limit(8)
            ->get();

        $couponOrderRevenue = Order::where('status', 'completed')
            ->where('created_at', '>=', $since)
            ->where('discount', '>', 0)
            ->sum('total');
        $totalDiscountGiven = (float) CouponUsage::where('created_at', '>=', $since)->sum('discount_amount');

        // ---- Referrals ----
        $referralsCreated = Referral::where('created_at', '>=', $since)->count();
        $referralsCompleted = Referral::whereIn('status', ['completed', 'rewarded'])->where('created_at', '>=', $since)->count();
        $referralRewards = (float) Referral::where('status', 'rewarded')->where('created_at', '>=', $since)->sum('reward_amount');
        $referredUserIds = Referral::whereIn('status', ['completed', 'rewarded'])->pluck('referred_id');
        $referralRevenue = Order::where('status', 'completed')
            ->where('created_at', '>=', $since)
            ->whereIn('user_id', $referredUserIds)
            ->sum('total');

        // ---- Points (coins) ----
        $pointsEarned = (int) PointsTransaction::earned()->where('created_at', '>=', $since)->sum('points');
        $pointsRedeemed = abs((int) PointsTransaction::spent()->where('created_at', '>=', $since)->sum('points'));
        $pointsOutstanding = (int) User::sum('points');
        $coinDiscountGiven = $pointsRedeemed > 0
            ? $pointsRedeemed / max(1, (int) \App\Models\Setting::get('points_per_dollar', 1000))
            : 0;

        return view('admin.analytics', compact(
            'range', 'revenue', 'ordersCount', 'aov', 'uniqueBuyers',
            'attempts', 'conversionRate', 'gateways',
            'trend', 'maxRevenue', 'topProducts',
            'couponUsages', 'couponOrderRevenue', 'totalDiscountGiven',
            'referralsCreated', 'referralsCompleted', 'referralRewards', 'referralRevenue',
            'pointsEarned', 'pointsRedeemed', 'pointsOutstanding', 'coinDiscountGiven'
        ));
    }
}
