@extends('layouts.admin')

@section('title', 'Analytics')

@section('content')
<div class="p-6 md:p-8">
    <div class="mb-8 flex flex-wrap items-center justify-between gap-4">
        <div>
            <h1 class="text-3xl font-bold text-foreground mb-2">Analytics</h1>
            <p class="text-muted-foreground">Sales trends, funnel, and coupon / referral / points performance</p>
        </div>
        <div class="flex gap-2">
            @foreach([7, 30, 90] as $r)
                <a href="{{ route('admin.analytics', ['range' => $r]) }}"
                    class="px-4 py-2 rounded-md text-sm font-medium border {{ $range === $r ? 'bg-primary text-primary-foreground border-primary' : 'border-input bg-background hover-elevate' }}">
                    {{ $r }}d
                </a>
            @endforeach
        </div>
    </div>

    <!-- KPI cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6 mb-8">
        @php
            $kpis = [
                ['label' => 'Revenue', 'value' => '$' . number_format($revenue, 2), 'sub' => $ordersCount . ' completed orders'],
                ['label' => 'Avg Order Value', 'value' => '$' . number_format($aov, 2), 'sub' => 'per completed order'],
                ['label' => 'Unique Buyers', 'value' => number_format($uniqueBuyers), 'sub' => 'distinct customers'],
                ['label' => 'Conversion Rate', 'value' => number_format($conversionRate, 1) . '%', 'sub' => $attempts . ' payment attempts'],
                ['label' => 'Discount Given', 'value' => '$' . number_format($totalDiscountGiven, 2), 'sub' => 'coupons in range'],
            ];
        @endphp
        @foreach($kpis as $kpi)
            <div class="shadcn-card rounded-xl border bg-card border-card-border text-card-foreground shadow-sm">
                <div class="p-6">
                    <h3 class="text-sm font-medium text-muted-foreground">{{ $kpi['label'] }}</h3>
                    <div class="text-2xl font-bold mt-2">{{ $kpi['value'] }}</div>
                    <p class="text-xs text-muted-foreground mt-1">{{ $kpi['sub'] }}</p>
                </div>
            </div>
        @endforeach
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <!-- Sales trend -->
        <div class="shadcn-card rounded-xl border bg-card border-card-border text-card-foreground shadow-sm">
            <div class="p-6"><h2 class="text-lg font-semibold">Revenue Trend - Last {{ $range }} Days</h2></div>
            <div class="p-6 pt-0">
                <div class="flex items-end gap-1 h-44">
                    @foreach($trend as $day)
                        <div class="flex-1 flex flex-col items-center gap-1 min-w-0" title="{{ $day['label'] }}: ${{ number_format($day['revenue'], 2) }} ({{ $day['orders'] }} orders)">
                            <div class="w-full rounded-t bg-gradient-to-t from-cyan-600 to-cyan-400"
                                style="height: {{ $maxRevenue > 0 ? max(3, round($day['revenue'] / $maxRevenue * 100)) : 3 }}%"></div>
                        </div>
                    @endforeach
                </div>
                <div class="flex justify-between text-[10px] text-muted-foreground mt-2">
                    <span>{{ $trend[0]['label'] ?? '' }}</span>
                    <span>{{ $trend[floor(count($trend) / 2)]['label'] ?? '' }}</span>
                    <span>{{ $trend[count($trend) - 1]['label'] ?? '' }}</span>
                </div>
            </div>
        </div>

        <!-- Conversion funnel by gateway -->
        <div class="shadcn-card rounded-xl border bg-card border-card-border text-card-foreground shadow-sm">
            <div class="p-6"><h2 class="text-lg font-semibold">Conversion Funnel by Gateway</h2></div>
            <div class="p-6 pt-0">
                @if(count($gateways) > 0)
                    <div class="space-y-4">
                        @foreach($gateways as $g)
                            @php $conv = $g['attempts'] > 0 ? ($g['orders'] / $g['attempts']) * 100 : 0; @endphp
                            <div>
                                <div class="flex items-center justify-between text-sm mb-1">
                                    <span class="font-medium capitalize">{{ str_replace(['-', '_'], ' ', $g['gateway']) }}</span>
                                    <span class="text-muted-foreground">{{ $g['orders'] }}/{{ $g['attempts'] }} - {{ number_format($conv, 1) }}%</span>
                                </div>
                                <div class="h-2.5 rounded-full bg-muted overflow-hidden">
                                    <div class="h-full rounded-full bg-gradient-to-r from-cyan-500 to-violet-500" style="width: {{ min(100, $conv) }}%"></div>
                                </div>
                                <p class="text-xs text-muted-foreground mt-1">${{ number_format($g['revenue'], 2) }} revenue</p>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-sm text-muted-foreground">No payment activity in this range.</p>
                @endif
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <!-- Top products -->
        <div class="shadcn-card rounded-xl border bg-card border-card-border text-card-foreground shadow-sm">
            <div class="p-6"><h2 class="text-lg font-semibold">Top Products</h2></div>
            <div class="p-6 pt-0">
                @if($topProducts->count() > 0)
                    <div class="space-y-3">
                        @foreach($topProducts as $item)
                            <div class="flex items-center justify-between gap-4">
                                <p class="text-sm font-medium truncate">{{ $item->product_name }}</p>
                                <p class="text-sm text-muted-foreground whitespace-nowrap">{{ $item->sold }} sold - ${{ number_format($item->revenue, 2) }}</p>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-sm text-muted-foreground">No completed orders yet.</p>
                @endif
            </div>
        </div>

        <!-- Coupon performance -->
        <div class="shadcn-card rounded-xl border bg-card border-card-border text-card-foreground shadow-sm">
            <div class="p-6"><h2 class="text-lg font-semibold">Coupon Performance</h2></div>
            <div class="p-6 pt-0">
                <p class="text-sm text-muted-foreground mb-4">${{ number_format($couponOrderRevenue, 2) }} revenue from discounted orders</p>
                @if($couponUsages->count() > 0)
                    <div class="space-y-3">
                        @foreach($couponUsages as $usage)
                            <div class="flex items-center justify-between gap-4">
                                <p class="text-sm font-medium font-mono">{{ $usage->coupon->code ?? 'deleted' }}</p>
                                <p class="text-sm text-muted-foreground whitespace-nowrap">{{ $usage->uses }} uses - ${{ number_format($usage->discount_given, 2) }} off</p>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-sm text-muted-foreground">No coupon usage in this range.</p>
                @endif
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Referrals -->
        <div class="shadcn-card rounded-xl border bg-card border-card-border text-card-foreground shadow-sm">
            <div class="p-6"><h2 class="text-lg font-semibold">Referrals</h2></div>
            <div class="p-6 pt-0">
                <div class="grid grid-cols-2 gap-4 text-sm">
                    <div><p class="text-muted-foreground text-xs">Signups</p><p class="text-xl font-bold">{{ $referralsCreated }}</p></div>
                    <div><p class="text-muted-foreground text-xs">Completed</p><p class="text-xl font-bold">{{ $referralsCompleted }}</p></div>
                    <div><p class="text-muted-foreground text-xs">Rewards paid</p><p class="text-xl font-bold">${{ number_format($referralRewards, 2) }}</p></div>
                    <div><p class="text-muted-foreground text-xs">Referred revenue</p><p class="text-xl font-bold">${{ number_format($referralRevenue, 2) }}</p></div>
                </div>
            </div>
        </div>

        <!-- Points / coins -->
        <div class="shadcn-card rounded-xl border bg-card border-card-border text-card-foreground shadow-sm">
            <div class="p-6"><h2 class="text-lg font-semibold">Points (Coins)</h2></div>
            <div class="p-6 pt-0">
                <div class="grid grid-cols-2 gap-4 text-sm">
                    <div><p class="text-muted-foreground text-xs">Earned</p><p class="text-xl font-bold">{{ number_format($pointsEarned) }}</p></div>
                    <div><p class="text-muted-foreground text-xs">Redeemed</p><p class="text-xl font-bold">{{ number_format($pointsRedeemed) }}</p></div>
                    <div><p class="text-muted-foreground text-xs">Outstanding liability</p><p class="text-xl font-bold">{{ number_format($pointsOutstanding) }}</p></div>
                    <div><p class="text-muted-foreground text-xs">Coin discounts</p><p class="text-xl font-bold">${{ number_format($coinDiscountGiven, 2) }}</p></div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
