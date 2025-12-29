@extends('layouts.app')

@section('content')
    <section class="py-16 bg-background">
        <div class="container mx-auto px-4">
            <div class="flex items-center justify-between mb-8">
                <div>
                    <h1 class="text-3xl font-bold font-display">Your Cart</h1>
                    <p class="text-sm text-muted-foreground">Review items before checkout.</p>
                </div>
                <a href="{{ route('bundles.index') }}" class="text-sm text-cyan-400 hover:text-cyan-300 inline-flex items-center gap-2">
                    <i data-lucide="arrow-left" class="w-4 h-4"></i>
                    Continue browsing
                </a>
            </div>

            @php
                $items = collect($cart);
                $total = $items->sum(fn ($item) => ($item['price'] ?? 0) * ($item['qty'] ?? 1));
            @endphp

            @if($items->isEmpty())
                <div class="glass-card rounded-3xl p-8 text-center">
                    <i data-lucide="shopping-cart" class="w-12 h-12 mx-auto mb-4 text-muted-foreground"></i>
                    <h2 class="text-xl font-bold font-display mb-2">Your cart is empty</h2>
                    <p class="text-muted-foreground mb-6">Add products to see them here.</p>
                    <a href="{{ route('bundles.index') }}" class="inline-flex items-center gap-2 px-6 py-3 rounded-xl bg-gradient-to-r from-cyan-500 to-violet-500 text-background font-semibold shadow-lg hover:shadow-xl transition">
                        Browse Products
                        <i data-lucide="arrow-right" class="w-4 h-4"></i>
                    </a>
                </div>
            @else
                <div class="grid lg:grid-cols-3 gap-6">
                    <div class="lg:col-span-2 space-y-4">
                        @foreach($items as $item)
                            <div class="glass-card rounded-2xl p-4 flex items-center gap-4">
                                <div class="w-20 h-20 rounded-xl bg-card overflow-hidden flex items-center justify-center">
                                    @if(!empty($item['image']))
                                        <img src="{{ $item['image'] }}" alt="{{ $item['title'] }}" class="w-full h-full object-cover">
                                    @else
                                        <i data-lucide="package" class="w-8 h-8 text-muted-foreground"></i>
                                    @endif
                                </div>
                                <div class="flex-1">
                                    <h3 class="font-semibold">{{ $item['title'] }}</h3>
                                    <p class="text-sm text-muted-foreground">Qty: {{ $item['qty'] ?? 1 }}</p>
                                </div>
                                <div class="text-right">
                                    <p class="text-lg font-bold">${{ number_format(($item['price'] ?? 0) * ($item['qty'] ?? 1), 2) }}</p>
                                    <p class="text-xs text-muted-foreground">${{ number_format($item['price'] ?? 0, 2) }} each</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <div class="glass-card rounded-2xl p-6 space-y-4">
                        <h3 class="text-xl font-bold font-display">Order Summary</h3>
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-muted-foreground">Items</span>
                            <span>{{ $items->count() }}</span>
                        </div>
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-muted-foreground">Subtotal</span>
                            <span>${{ number_format($total, 2) }}</span>
                        </div>
                        <div class="pt-4 border-t border-border flex items-center justify-between">
                            <span class="text-lg font-semibold">Total</span>
                            <span class="text-2xl font-bold gradient-text">${{ number_format($total, 2) }}</span>
                        </div>
                        <a href="{{ route('checkout.show') }}" class="w-full inline-flex items-center justify-center gap-2 px-6 py-3 rounded-xl bg-gradient-to-r from-cyan-500 to-violet-500 text-background font-semibold shadow-lg hover:shadow-xl transition">
                            Checkout
                            <i data-lucide="arrow-right" class="w-4 h-4"></i>
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </section>
@endsection

