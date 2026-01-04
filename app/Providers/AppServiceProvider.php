<?php

namespace App\Providers;

use App\Models\Menu;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Force HTTPS in production
        if (config('app.env') === 'production') {
            URL::forceScheme('https');
        }

        View::composer('*', function ($view) {
            $cart = Session::get('cart', []);
            $cartItems = is_array($cart) ? count($cart) : 0;
            $cartQuantity = is_array($cart)
                ? collect($cart)->sum(fn ($item) => $item['qty'] ?? 0)
                : 0;

            $userPoints = auth()->check() ? auth()->user()->getPoints() : 0;

            $dailyBonusClaimed = session('daily_bonus_claimed');
            if ($dailyBonusClaimed) {
                session()->forget('daily_bonus_claimed');
            }

            $view->with('navMenus', Cache::remember('view.menus', 3600, function () {
                return Menu::active()->ordered()->get();
            }))
            ->with('cartItems', $cartItems)
            ->with('cartQuantity', $cartQuantity)
            ->with('userPoints', $userPoints)
            ->with('dailyBonusClaimed', $dailyBonusClaimed);
        });
    }
}
