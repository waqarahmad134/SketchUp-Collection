@extends('layouts.admin')

@section('title', 'Coupons')

@section('content')
<div class="p-6 md:p-8">
    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-3xl font-bold text-foreground mb-2">
                Coupons
            </h1>
            <p class="text-muted-foreground">
                Manage discount coupons and promotional codes
            </p>
        </div>
        <a href="{{ route('admin.coupons.create') }}">
            <button class="inline-flex items-center justify-center gap-2 whitespace-nowrap rounded-md text-sm font-medium min-h-10 px-4 py-2 bg-primary text-primary-foreground border border-primary-border hover-elevate active-elevate-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                New Coupon
            </button>
        </a>
    </div>

    <div class="shadcn-card rounded-xl border bg-card border-card-border text-card-foreground shadow-sm p-6">
        @if($coupons->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b">
                            <th class="text-left p-3 font-semibold">Code</th>
                            <th class="text-left p-3 font-semibold">Type</th>
                            <th class="text-left p-3 font-semibold">Value</th>
                            <th class="text-left p-3 font-semibold">Min Order</th>
                            <th class="text-left p-3 font-semibold">Usage</th>
                            <th class="text-left p-3 font-semibold">Expires</th>
                            <th class="text-left p-3 font-semibold">Status</th>
                            <th class="text-right p-3 font-semibold">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($coupons as $coupon)
                        <tr class="border-b hover:bg-muted/50">
                            <td class="p-3">
                                <span class="font-medium font-mono">{{ $coupon->code }}</span>
                            </td>
                            <td class="p-3">
                                <span class="px-2 py-1 rounded text-xs bg-muted capitalize">{{ $coupon->type }}</span>
                            </td>
                            <td class="p-3">
                                @if($coupon->type === 'percentage')
                                    <span class="font-medium">{{ $coupon->value }}%</span>
                                @else
                                    <span class="font-medium">${{ number_format($coupon->value, 2) }}</span>
                                @endif
                            </td>
                            <td class="p-3 text-sm text-muted-foreground">
                                @if($coupon->min_order > 0)
                                    ${{ number_format($coupon->min_order, 2) }}
                                @else
                                    —
                                @endif
                            </td>
                            <td class="p-3 text-sm text-muted-foreground">
                                {{ $coupon->used_count }}
                                @if($coupon->usage_limit)
                                    / {{ $coupon->usage_limit }}
                                @else
                                    / ∞
                                @endif
                            </td>
                            <td class="p-3 text-sm text-muted-foreground">
                                @if($coupon->expires_at)
                                    {{ $coupon->expires_at->format('d/m/Y') }}
                                    @if($coupon->expires_at->isPast())
                                        <span class="text-destructive">(Expired)</span>
                                    @endif
                                @else
                                    Never
                                @endif
                            </td>
                            <td class="p-3">
                                <span class="px-2 py-1 rounded text-xs {{ $coupon->is_active ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : 'bg-gray-100 text-gray-800 dark:bg-gray-800 dark:text-gray-200' }}">
                                    {{ $coupon->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td class="p-3">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('admin.coupons.edit', $coupon->id) }}">
                                        <button class="inline-flex items-center justify-center gap-2 whitespace-nowrap rounded-md text-sm font-medium min-h-9 w-9 border border-input bg-background text-foreground hover-elevate active-elevate-2">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                            </svg>
                                        </button>
                                    </a>
                                    <form method="POST" action="{{ route('admin.coupons.destroy', $coupon->id) }}" onsubmit="return confirm('Are you sure? This action cannot be undone.');" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="inline-flex items-center justify-center gap-2 whitespace-nowrap rounded-md text-sm font-medium min-h-9 w-9 border border-input bg-background text-foreground hover-elevate active-elevate-2">
                                            <svg class="w-4 h-4 text-destructive" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-center py-12">
                <p class="text-muted-foreground mb-4">
                    No coupons yet
                </p>
                <a href="{{ route('admin.coupons.create') }}">
                    <button class="inline-flex items-center justify-center gap-2 whitespace-nowrap rounded-md text-sm font-medium min-h-10 px-4 py-2 bg-primary text-primary-foreground border border-primary-border hover-elevate active-elevate-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                        Create First Coupon
                    </button>
                </a>
            </div>
        @endif
    </div>
</div>
@endsection
