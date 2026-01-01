<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Cache;

class StatsOverviewWidget extends BaseWidget
{
    protected function getStats(): array
    {
        // Cache stats for 5 minutes to avoid hitting database on every load
        return Cache::remember('dashboard_stats', 300, function () {
            // Get all stats in fewer queries
            $totalRevenue = Transaction::where('status', 'completed')->sum('amount');
            $totalOrders = Order::count();
            $pendingOrders = Order::where('status', 'pending')->count();
            $totalUsers = User::count();
            $totalProducts = Product::where('is_active', true)->count();
            $totalBundles = Product::where('is_bundle', true)->where('is_active', true)->count();

            return [
                Stat::make('Total Revenue', '$' . number_format($totalRevenue, 2))
                    ->description('Total completed transactions')
                    ->descriptionIcon('heroicon-m-arrow-trending-up')
                    ->color('success')
                    ->chart([7, 3, 4, 5, 6, 3, 5, 3]),
                
                Stat::make('Total Orders', number_format($totalOrders))
                    ->description($pendingOrders . ' pending')
                    ->descriptionIcon('heroicon-m-shopping-bag')
                    ->color('warning'),
                
                Stat::make('Total Users', number_format($totalUsers))
                    ->description('Registered users')
                    ->descriptionIcon('heroicon-m-users')
                    ->color('info'),
                
                Stat::make('Total Products', number_format($totalProducts))
                    ->description($totalBundles . ' bundles')
                    ->descriptionIcon('heroicon-m-cube')
                    ->color('success'),
            ];
        });
    }
}

