@extends('layouts.app')

@section('content')
    <section class="py-16 bg-background">
        <div class="container mx-auto px-4">
            @php
                $items = collect($cart);
                $subtotal = $items->sum(fn ($item) => ($item['price'] ?? 0) * ($item['qty'] ?? 1));
                $discountAmount = $discount ?? 0;
                $coinDiscountAmount = $coinDiscount ?? 0;
                $total = $subtotal - $discountAmount - $coinDiscountAmount;
            @endphp

            <div class="flex items-center justify-between mb-8">
                <div>
                    <h1 class="text-3xl font-bold font-display">Your Cart</h1>
                    <p class="text-sm text-muted-foreground">Review items before checkout.</p>
                </div>
                <div class="flex items-center gap-4">
                    @if(!$items->isEmpty())
                        <form action="{{ route('cart.clear') }}" method="POST" id="clearCartForm" class="inline">
                            @csrf
                            <button type="submit" class="text-sm text-red-400 hover:text-red-300 inline-flex items-center gap-2">
                                <i data-lucide="trash-2" class="w-4 h-4"></i>
                                Empty Cart
                            </button>
                        </form>
                    @endif
                    <a href="{{ route('bundles.index') }}" class="text-sm text-cyan-400 hover:text-cyan-300 inline-flex items-center gap-2">
                        <i data-lucide="arrow-left" class="w-4 h-4"></i>
                        Continue browsing
                    </a>
                </div>
            </div>

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
                                <div class="text-right flex items-center gap-4">
                                    <div>
                                        <p class="text-lg font-bold">${{ number_format(($item['price'] ?? 0) * ($item['qty'] ?? 1), 2) }}</p>
                                        <p class="text-xs text-muted-foreground">${{ number_format($item['price'] ?? 0, 2) }} each</p>
                                    </div>
                                    <form action="{{ route('cart.remove', $item['id']) }}" method="POST" class="inline remove-item-form" data-item-title="{{ $item['title'] }}">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-400 hover:text-red-300 p-2 rounded-lg hover:bg-red-500/10 transition" title="Remove item">
                                            <i data-lucide="trash-2" class="w-5 h-5"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <div class="glass-card rounded-2xl p-6 space-y-4">
                        <h3 class="text-xl font-bold font-display">Order Summary</h3>
                        
                        {{-- Coupon Section --}}
                        <div class="space-y-3 pb-4 border-b border-border">
                            @if($coupon)
                                <div class="flex items-center justify-between p-3 rounded-xl bg-cyan-500/10 border border-cyan-500/20">
                                    <div class="flex-1">
                                        <div class="flex items-center gap-2">
                                            <i data-lucide="ticket" class="w-4 h-4 text-cyan-400"></i>
                                            <span class="font-semibold text-sm">{{ $coupon->code }}</span>
                                        </div>
                                        <p class="text-xs text-muted-foreground mt-1">
                                            {{ $coupon->type === 'percentage' ? $coupon->value . '% off' : '$' . number_format($coupon->value, 2) . ' off' }}
                                        </p>
                                    </div>
                                    <form action="{{ route('cart.coupon.remove') }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="text-red-400 hover:text-red-300 p-1" title="Remove coupon">
                                            <i data-lucide="x" class="w-4 h-4"></i>
                                        </button>
                                    </form>
                                </div>
                            @else
                                <form action="{{ route('cart.coupon.apply') }}" method="POST" class="space-y-2" id="couponForm">
                                    @csrf
                                    <div class="flex gap-2">
                                        <input 
                                            type="text" 
                                            name="code" 
                                            id="couponCode"
                                            placeholder="Enter coupon code"
                                            class="flex-1 px-4 py-2 rounded-xl bg-card border border-border focus:border-cyan-500 focus:ring-2 focus:ring-cyan-500/30 outline-none text-sm"
                                            value="{{ old('code') }}"
                                        >
                                        <button 
                                            type="submit" 
                                            class="px-4 py-2 rounded-xl bg-gradient-to-r from-cyan-500 to-violet-500 text-background font-semibold text-sm hover:shadow-lg transition"
                                        >
                                            Apply
                                        </button>
                                    </div>
                                    @error('coupon')
                                        <p class="text-xs text-red-400">{{ $message }}</p>
                                    @enderror
                                </form>
                            @endif
                        </div>

                        {{-- SKP Coins Section --}}
                        @auth
                        <div class="space-y-3 pb-4 border-b border-border">
                            @if($coinsToUse > 0)
                                <div class="flex items-center justify-between p-3 rounded-xl bg-yellow-500/10 border border-yellow-500/20">
                                    <div class="flex-1">
                                        <div class="flex items-center gap-2">
                                            <i data-lucide="coins" class="w-4 h-4 text-yellow-400"></i>
                                            <span class="font-semibold text-sm">{{ number_format($coinsToUse, 0) }} SKP Coins</span>
                                        </div>
                                        <p class="text-xs text-muted-foreground mt-1">
                                            ${{ number_format($coinDiscountAmount, 2) }} discount applied
                                        </p>
                                    </div>
                                    <form action="{{ route('cart.coins.remove') }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="text-red-400 hover:text-red-300 p-1" title="Remove coins">
                                            <i data-lucide="x" class="w-4 h-4"></i>
                                        </button>
                                    </form>
                                </div>
                            @else
                                <div class="space-y-2">
                                    <div class="flex items-center justify-between mb-2">
                                        <span class="text-sm font-semibold">Your SKP Coins</span>
                                        <span class="text-sm text-yellow-400 font-bold">{{ number_format($userPoints ?? 0, 0) }} SKP</span>
                                    </div>
                                    <form action="{{ route('cart.coins.apply') }}" method="POST" class="space-y-2" id="coinsForm">
                                        @csrf
                                        <div class="flex gap-2">
                                            <input 
                                                type="number" 
                                                name="coins" 
                                                id="coinsInput"
                                                placeholder="Enter coins to use"
                                                min="1"
                                                max="{{ $userPoints ?? 0 }}"
                                                class="flex-1 px-4 py-2 rounded-xl bg-card border border-border focus:border-yellow-500 focus:ring-2 focus:ring-yellow-500/30 outline-none text-sm"
                                                value="{{ old('coins') }}"
                                            >
                                            <button 
                                                type="submit" 
                                                class="px-4 py-2 rounded-xl bg-gradient-to-r from-yellow-500 to-orange-500 text-background font-semibold text-sm hover:shadow-lg transition"
                                                {{ ($userPoints ?? 0) < 1 ? 'disabled' : '' }}
                                            >
                                                Use
                                            </button>
                                        </div>
                                        @error('coins')
                                            <p class="text-xs text-red-400">{{ $message }}</p>
                                        @enderror
                                        @if(($userPoints ?? 0) > 0)
                                            @php
                                                $maxCoinsValue = ($subtotal - $discountAmount) * ($pointsPerDollar ?? 1000);
                                                $maxCoinsToUse = min($userPoints, $maxCoinsValue);
                                            @endphp
                                            <p class="text-xs text-muted-foreground">
                                                You can use up to {{ number_format($maxCoinsToUse, 0) }} coins (${{ number_format($maxCoinsToUse / ($pointsPerDollar ?? 1000), 2) }} value)
                                            </p>
                                        @else
                                            <p class="text-xs text-muted-foreground">You don't have any coins to use.</p>
                                        @endif
                                    </form>
                                </div>
                            @endif
                        </div>
                        @endauth

                        <div class="flex items-center justify-between text-sm">
                            <span class="text-muted-foreground">Items</span>
                            <span>{{ $items->count() }}</span>
                        </div>
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-muted-foreground">Subtotal</span>
                            <span>${{ number_format($subtotal, 2) }}</span>
                        </div>
                        @if($discountAmount > 0)
                            <div class="flex items-center justify-between text-sm text-cyan-400">
                                <span>Coupon Discount</span>
                                <span>-${{ number_format($discountAmount, 2) }}</span>
                            </div>
                        @endif
                        @if($coinDiscountAmount > 0)
                            <div class="flex items-center justify-between text-sm text-yellow-400">
                                <span>Coins Discount</span>
                                <span>-${{ number_format($coinDiscountAmount, 2) }}</span>
                            </div>
                        @endif
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

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Handle empty cart form
            const clearCartForm = document.getElementById('clearCartForm');
            if (clearCartForm) {
                clearCartForm.addEventListener('submit', function(e) {
                    e.preventDefault();
                    
                    Swal.fire({
                        title: 'Empty Cart?',
                        text: 'Are you sure you want to remove all items from your cart?',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#ef4444',
                        cancelButtonColor: '#6b7280',
                        confirmButtonText: 'Yes, empty cart',
                        cancelButtonText: 'Cancel'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            clearCartForm.submit();
                        }
                    });
                });
            }

            // Handle remove item forms
            const removeItemForms = document.querySelectorAll('.remove-item-form');
            removeItemForms.forEach(function(form) {
                form.addEventListener('submit', function(e) {
                    e.preventDefault();
                    
                    const itemTitle = this.getAttribute('data-item-title');
                    
                    Swal.fire({
                        title: 'Remove Item?',
                        text: `Are you sure you want to remove "${itemTitle}" from your cart?`,
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#ef4444',
                        cancelButtonColor: '#6b7280',
                        confirmButtonText: 'Yes, remove it',
                        cancelButtonText: 'Cancel'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            form.submit();
                        }
                    });
                });
            });
        });
    </script>
    @endpush
@endsection

