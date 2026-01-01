<?php

namespace App\Providers;

use App\Models\Menu;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Session;
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
        View::composer('*', function ($view) {
            $cart = Session::get('cart', []);
            $cartItems = is_array($cart) ? count($cart) : 0;
            $cartQuantity = is_array($cart)
                ? collect($cart)->sum(fn ($item) => $item['qty'] ?? 0)
                : 0;

            $view->with('navMenus', Cache::remember('view.menus', 3600, function () {
                return Menu::active()->ordered()->get();
            }))
            ->with('cartItems', $cartItems)
            ->with('cartQuantity', $cartQuantity);
        });
    }
}
