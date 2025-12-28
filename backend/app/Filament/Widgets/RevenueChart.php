<?php

namespace App\Filament\Widgets;

use App\Models\Transaction;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;

class RevenueChart extends ChartWidget
{
    protected ?string $heading = 'Revenue Overview';

    protected static ?int $sort = 3;

    protected function getData(): array
    {
        // Cache chart data for 5 minutes
        $transactions = Cache::remember('revenue_chart_data', 300, function () {
            return Transaction::selectRaw('DATE(created_at) as date, SUM(amount) as total')
                ->where('status', 'completed')
                ->where('created_at', '>=', now()->subDays(30))
                ->groupBy('date')
                ->orderBy('date')
                ->get();
        });

        return [
            'datasets' => [
                [
                    'label' => 'Revenue ($)',
                    'data' => $transactions->pluck('total')->toArray(),
                    'backgroundColor' => 'rgba(75, 192, 192, 0.2)',
                    'borderColor' => 'rgba(75, 192, 192, 1)',
                ],
            ],
            'labels' => $transactions->pluck('date')->map(fn ($date) => Carbon::parse($date)->format('M d'))->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}

