<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Post;
use App\Models\PostCategory;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;

class AdminController extends Controller
{
    public function showLogin()
    {
        return view('admin.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // Brute-force protection: 5 attempts per email+IP, then 5 minute lockout
        $throttleKey = 'admin-login:' . strtolower($request->input('email')) . '|' . $request->ip();

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);

            throw ValidationException::withMessages([
                'email' => "Too many login attempts. Please try again in {$seconds} seconds.",
            ]);
        }

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            RateLimiter::clear($throttleKey);
            $request->session()->regenerate();
            return redirect()->intended(route('admin.dashboard'));
        }

        RateLimiter::hit($throttleKey, 300);

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('admin.login');
    }

    public function dashboard()
    {
        $posts = Post::with('category')->latest()->get();
        $categories = PostCategory::active()->get();
        $tags = Tag::where('type', 'post')->active()->get();

        // Add view_count accessor for compatibility with views
        $posts->each(function ($post) {
            $post->view_count = $post->views;
        });

        // Store revenue stats
        $completedOrders = Order::where('status', 'completed');
        $totalRevenue = (clone $completedOrders)->sum('total');
        $ordersCount = (clone $completedOrders)->count();
        $customersCount = Order::where('status', 'completed')->distinct('user_id')->count('user_id');
        $avgOrderValue = $ordersCount > 0 ? $totalRevenue / $ordersCount : 0;

        // Revenue by day for the last 14 days
        $revenueByDay = Order::where('status', 'completed')
            ->where('created_at', '>=', now()->subDays(13)->startOfDay())
            ->select(DB::raw('DATE(created_at) as day'), DB::raw('SUM(total) as revenue'), DB::raw('COUNT(*) as orders'))
            ->groupBy('day')
            ->orderBy('day')
            ->get()
            ->keyBy('day');

        $revenueChart = [];
        $maxRevenue = 0;
        for ($i = 13; $i >= 0; $i--) {
            $day = now()->subDays($i)->toDateString();
            $revenue = (float) ($revenueByDay[$day]->revenue ?? 0);
            $maxRevenue = max($maxRevenue, $revenue);
            $revenueChart[] = [
                'label' => now()->subDays($i)->format('M d'),
                'revenue' => $revenue,
                'orders' => (int) ($revenueByDay[$day]->orders ?? 0),
            ];
        }

        // Top selling products
        $topProducts = OrderItem::select('product_id', 'product_name', DB::raw('SUM(quantity) as sold'), DB::raw('SUM(total) as revenue'))
            ->whereHas('order', fn ($q) => $q->where('status', 'completed'))
            ->groupBy('product_id', 'product_name')
            ->orderByDesc('sold')
            ->limit(5)
            ->get();

        return view('admin.dashboard', compact(
            'posts', 'categories', 'tags',
            'totalRevenue', 'ordersCount', 'customersCount', 'avgOrderValue',
            'revenueChart', 'maxRevenue', 'topProducts'
        ));
    }
}
