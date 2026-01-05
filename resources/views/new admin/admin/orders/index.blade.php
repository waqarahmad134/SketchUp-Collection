@extends('layouts.admin')

@section('title', 'Orders')

@section('content')
<div class="p-6 md:p-8">
    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-3xl font-bold text-foreground mb-2">
                Orders
            </h1>
            <p class="text-muted-foreground">
                Manage customer orders
            </p>
        </div>
        <a href="{{ route('newadmin.orders.create') }}">
            <button class="inline-flex items-center justify-center gap-2 whitespace-nowrap rounded-md text-sm font-medium min-h-10 px-4 py-2 bg-primary text-primary-foreground border border-primary-border hover-elevate active-elevate-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                New Order
            </button>
        </a>
    </div>

    <div class="shadcn-card rounded-xl border bg-card border-card-border text-card-foreground shadow-sm p-6">
        @if($orders->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b">
                            <th class="text-left p-3 font-semibold">Order #</th>
                            <th class="text-left p-3 font-semibold">Customer</th>
                            <th class="text-left p-3 font-semibold">Email</th>
                            <th class="text-left p-3 font-semibold">Items</th>
                            <th class="text-left p-3 font-semibold">Total</th>
                            <th class="text-left p-3 font-semibold">Status</th>
                            <th class="text-left p-3 font-semibold">Payment</th>
                            <th class="text-left p-3 font-semibold">Date</th>
                            <th class="text-right p-3 font-semibold">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($orders as $order)
                        <tr class="border-b hover:bg-muted/50">
                            <td class="p-3">
                                <span class="font-mono font-medium">{{ $order->order_number }}</span>
                            </td>
                            <td class="p-3">
                                <span class="font-medium">{{ $order->customer_name }}</span>
                                @if($order->user)
                                    <span class="text-xs text-muted-foreground">({{ $order->user->name }})</span>
                                @endif
                            </td>
                            <td class="p-3 text-sm text-muted-foreground">{{ $order->customer_email }}</td>
                            <td class="p-3">
                                <span class="text-sm">{{ $order->items->count() }} item(s)</span>
                            </td>
                            <td class="p-3">
                                <span class="font-medium">${{ number_format($order->total, 2) }}</span>
                                <span class="text-xs text-muted-foreground">{{ $order->currency }}</span>
                            </td>
                            <td class="p-3">
                                <span class="px-2 py-1 rounded text-xs font-medium capitalize
                                    @if($order->status === 'completed') bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200
                                    @elseif($order->status === 'cancelled' || $order->status === 'refunded') bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200
                                    @elseif($order->status === 'processing') bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200
                                    @else bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200
                                    @endif">
                                    {{ $order->status }}
                                </span>
                            </td>
                            <td class="p-3">
                                <span class="px-2 py-1 rounded text-xs font-medium capitalize
                                    @if($order->payment_status === 'paid') bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200
                                    @elseif($order->payment_status === 'failed' || $order->payment_status === 'refunded') bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200
                                    @else bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200
                                    @endif">
                                    {{ $order->payment_status }}
                                </span>
                            </td>
                            <td class="p-3 text-sm text-muted-foreground">{{ $order->created_at->format('d/m/Y H:i') }}</td>
                            <td class="p-3">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('newadmin.orders.show', $order->id) }}">
                                        <button class="inline-flex items-center justify-center gap-2 whitespace-nowrap rounded-md text-sm font-medium min-h-9 w-9 border border-input bg-background text-foreground hover-elevate active-elevate-2">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                            </svg>
                                        </button>
                                    </a>
                                    <a href="{{ route('newadmin.orders.edit', $order->id) }}">
                                        <button class="inline-flex items-center justify-center gap-2 whitespace-nowrap rounded-md text-sm font-medium min-h-9 w-9 border border-input bg-background text-foreground hover-elevate active-elevate-2">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                            </svg>
                                        </button>
                                    </a>
                                    <form method="POST" action="{{ route('newadmin.orders.destroy', $order->id) }}" onsubmit="return confirm('Are you sure? This action cannot be undone.');" class="inline">
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
                    No orders yet
                </p>
                <a href="{{ route('newadmin.orders.create') }}">
                    <button class="inline-flex items-center justify-center gap-2 whitespace-nowrap rounded-md text-sm font-medium min-h-10 px-4 py-2 bg-primary text-primary-foreground border border-primary-border hover-elevate active-elevate-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                        Create First Order
                    </button>
                </a>
            </div>
        @endif
    </div>
</div>
@endsection
