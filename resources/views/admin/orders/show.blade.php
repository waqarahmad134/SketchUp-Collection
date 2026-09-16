@extends('layouts.admin')

@section('title', 'Order Details')

@section('content')
<div class="p-6 md:p-8">
    <div class="mb-8">
        <a href="{{ route('admin.orders.index') }}">
            <button class="inline-flex items-center justify-center gap-2 whitespace-nowrap rounded-md text-sm font-medium min-h-9 w-9 border border-input bg-background text-foreground hover-elevate active-elevate-2 mb-4">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
            </button>
        </a>
        <h1 class="text-3xl font-bold text-foreground mb-2">
            Order Details
        </h1>
        <p class="text-muted-foreground">
            Order #{{ $order->order_number }}
        </p>
    </div>

    <div class="space-y-6">
        <!-- Order Information -->
        <div class="shadcn-card rounded-xl border bg-card border-card-border text-card-foreground shadow-sm p-6">
            <h2 class="text-xl font-semibold mb-4">Order Information</h2>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <p class="text-sm text-muted-foreground">Order Number</p>
                    <p class="font-mono font-medium">{{ $order->order_number }}</p>
                </div>
                <div>
                    <p class="text-sm text-muted-foreground">Status</p>
                    <span class="px-2 py-1 rounded text-xs font-medium capitalize
                        @if($order->status === 'completed') bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200
                        @elseif($order->status === 'cancelled' || $order->status === 'refunded') bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200
                        @elseif($order->status === 'processing') bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200
                        @else bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200
                        @endif">
                        {{ $order->status }}
                    </span>
                </div>
                <div>
                    <p class="text-sm text-muted-foreground">Payment Status</p>
                    <span class="px-2 py-1 rounded text-xs font-medium capitalize
                        @if($order->payment_status === 'paid') bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200
                        @elseif($order->payment_status === 'failed' || $order->payment_status === 'refunded') bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200
                        @else bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200
                        @endif">
                        {{ $order->payment_status }}
                    </span>
                </div>
                <div>
                    <p class="text-sm text-muted-foreground">Date</p>
                    <p>{{ $order->created_at->format('d/m/Y H:i') }}</p>
                </div>
            </div>
        </div>

        <!-- Customer Information -->
        <div class="shadcn-card rounded-xl border bg-card border-card-border text-card-foreground shadow-sm p-6">
            <h2 class="text-xl font-semibold mb-4">Customer Information</h2>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <p class="text-sm text-muted-foreground">Name</p>
                    <p class="font-medium">{{ $order->customer_name }}</p>
                </div>
                <div>
                    <p class="text-sm text-muted-foreground">Email</p>
                    <p>{{ $order->customer_email }}</p>
                </div>
                @if($order->customer_phone)
                <div>
                    <p class="text-sm text-muted-foreground">Phone</p>
                    <p>{{ $order->customer_phone }}</p>
                </div>
                @endif
                @if($order->user)
                <div>
                    <p class="text-sm text-muted-foreground">User Account</p>
                    <p>{{ $order->user->name }} ({{ $order->user->email }})</p>
                </div>
                @endif
                @if($order->customer_note)
                <div class="col-span-2">
                    <p class="text-sm text-muted-foreground">Note</p>
                    <p>{{ $order->customer_note }}</p>
                </div>
                @endif
            </div>
        </div>

        <!-- Order Items -->
        <div class="shadcn-card rounded-xl border bg-card border-card-border text-card-foreground shadow-sm p-6">
            <h2 class="text-xl font-semibold mb-4">Order Items</h2>
            @if($order->items->count() > 0)
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="border-b">
                                <th class="text-left p-3 font-semibold">Product</th>
                                <th class="text-left p-3 font-semibold">Quantity</th>
                                <th class="text-left p-3 font-semibold">Price</th>
                                <th class="text-right p-3 font-semibold">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($order->items as $item)
                            <tr class="border-b">
                                <td class="p-3">
                                    <div class="flex items-center gap-3">
                                        @if($item->product_image)
                                            <img src="{{ asset('storage/' . $item->product_image) }}" alt="{{ $item->product_name }}" class="w-12 h-12 rounded object-cover">
                                        @endif
                                        <div>
                                            <p class="font-medium">{{ $item->product_name }}</p>
                                            @if($item->product)
                                                <p class="text-xs text-muted-foreground">{{ $item->product->slug }}</p>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="p-3">{{ $item->quantity }}</td>
                                <td class="p-3">${{ number_format($item->price, 2) }}</td>
                                <td class="p-3 text-right font-medium">${{ number_format($item->total, 2) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p class="text-muted-foreground">No items in this order</p>
            @endif
        </div>

        <!-- Pricing Summary -->
        <div class="shadcn-card rounded-xl border bg-card border-card-border text-card-foreground shadow-sm p-6">
            <h2 class="text-xl font-semibold mb-4">Pricing Summary</h2>
            <div class="space-y-2">
                <div class="flex justify-between">
                    <span class="text-muted-foreground">Subtotal</span>
                    <span class="font-medium">${{ number_format($order->subtotal, 2) }}</span>
                </div>
                @if($order->tax > 0)
                <div class="flex justify-between">
                    <span class="text-muted-foreground">Tax</span>
                    <span class="font-medium">${{ number_format($order->tax, 2) }}</span>
                </div>
                @endif
                @if($order->discount > 0)
                <div class="flex justify-between">
                    <span class="text-muted-foreground">Discount</span>
                    <span class="font-medium text-green-600">-${{ number_format($order->discount, 2) }}</span>
                </div>
                @endif
                <div class="flex justify-between pt-2 border-t">
                    <span class="font-semibold">Total</span>
                    <span class="font-bold text-lg">${{ number_format($order->total, 2) }} {{ $order->currency }}</span>
                </div>
            </div>
        </div>

        <!-- Actions -->
        <div class="flex gap-3">
            <a href="{{ route('admin.orders.edit', $order->id) }}">
                <button class="inline-flex items-center justify-center gap-2 whitespace-nowrap rounded-md text-sm font-medium min-h-10 px-4 py-2 bg-primary text-primary-foreground border border-primary-border hover-elevate active-elevate-2">
                    Edit Order
                </button>
            </a>
            <a href="{{ route('admin.orders.index') }}">
                <button class="inline-flex items-center justify-center gap-2 whitespace-nowrap rounded-md text-sm font-medium min-h-10 px-4 py-2 border border-input bg-background text-foreground hover-elevate active-elevate-2">
                    Back to Orders
                </button>
            </a>
        </div>
    </div>
</div>
@endsection
