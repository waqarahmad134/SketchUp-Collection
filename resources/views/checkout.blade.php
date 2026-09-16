@extends('layouts.app')

@section('content')
    <section class="py-16 bg-background">
        <div class="container mx-auto px-4">
            <div class="max-w-3xl mx-auto space-y-6">
                <div class="glass-card rounded-3xl p-8">
                    <div>
                        <h1 class="text-3xl font-bold font-display">Checkout</h1>
                        <p class="text-sm text-muted-foreground">Review your order and complete payment.</p>
                    </div>
                </div>

                <div class="grid lg:grid-cols-3 gap-6">
                    <div class="lg:col-span-2 space-y-6">
                        <div class="glass-card rounded-2xl p-6">
                            <h2 class="text-xl font-bold mb-4">Order Summary</h2>
                            <div class="space-y-3">
                                @foreach($cart as $item)
                                    <div class="flex items-center justify-between py-2 border-b border-border/50">
                                        <div class="flex-1">
                                            <p class="font-semibold">{{ $item['title'] }}</p>
                                            <p class="text-sm text-muted-foreground">Qty: {{ $item['qty'] ?? 1 }}</p>
                                        </div>
                                        <p class="font-bold">{{ \App\Support\Currency::format(($item['price'] ?? 0) * ($item['qty'] ?? 1)) }}</p>
                                    </div>
                                @endforeach
                            </div>
                            
                            <div class="mt-4 pt-4 border-t border-border space-y-2">
                                <div class="flex items-center justify-between text-sm">
                                    <span class="text-muted-foreground">Subtotal</span>
                                    <span>{{ \App\Support\Currency::format($subtotal) }}</span>
                                </div>
                                @if($discount > 0)
                                    <div class="flex items-center justify-between text-sm text-cyan-400">
                                        <span>Coupon Discount</span>
                                        <span>-{{ \App\Support\Currency::format($discount) }}</span>
                                    </div>
                                @endif
                                @if($coinDiscount > 0)
                                    <div class="flex items-center justify-between text-sm text-yellow-400">
                                        <span>Coins Discount ({{ number_format($coinsToUse, 0) }} SKP)</span>
                                        <span>-{{ \App\Support\Currency::format($coinDiscount) }}</span>
                                    </div>
                                @endif
                                <div class="flex items-center justify-between pt-2 border-t border-border">
                                    <span class="text-lg font-semibold">Total</span>
                                    <span class="text-2xl font-bold gradient-text">{{ \App\Support\Currency::format($total) }}</span>
                                </div>
                            </div>
                        </div>

                        @auth
                        @if($userPoints > 0 && $coinsToUse == 0)
                        <div class="glass-card rounded-2xl p-6 border border-yellow-500/20 bg-yellow-500/5">
                            <div class="flex items-center justify-between">
                                <div>
                                    <h3 class="font-semibold mb-1">Use Your SKP Coins</h3>
                                    <p class="text-sm text-muted-foreground">You have {{ number_format($userPoints, 0) }} SKP coins available</p>
                                </div>
                                <a href="{{ route('cart.show') }}" class="px-4 py-2 rounded-xl bg-gradient-to-r from-yellow-500 to-orange-500 text-background font-semibold text-sm hover:shadow-lg transition">
                                    Apply Coins
                                </a>
                            </div>
                        </div>
                        @endif
                        @endauth
                    </div>

                    <div class="glass-card rounded-2xl p-6">
                        <h2 class="text-xl font-bold mb-4">Payment</h2>
                        @if(session('status'))
                            <div class="mb-4 px-4 py-3 rounded-xl bg-green-500/10 text-green-400 text-sm">
                                {{ session('status') }}
                            </div>
                        @endif
                        @if($errors->has('payment'))
                            <div class="mb-4 px-4 py-3 rounded-xl bg-red-500/10 text-red-400 text-sm">
                                {{ $errors->first('payment') }}
                            </div>
                        @endif
                        @if($errors->has('cart'))
                            <div class="mb-4 px-4 py-3 rounded-xl bg-red-500/10 text-red-400 text-sm">
                                {{ $errors->first('cart') }}
                            </div>
                        @endif
                        <div class="space-y-4">
                            <form method="GET" action="{{ route('checkout.stripe.start') }}" target="_blank">
                                <button type="submit" class="w-full inline-flex items-center justify-center gap-2 px-6 py-3 rounded-xl bg-gradient-to-r from-cyan-500 to-violet-500 text-background font-semibold shadow-lg hover:shadow-xl transition">
                                    Pay {{ \App\Support\Currency::format($total) }} with Stripe
                                    <i data-lucide="arrow-up-right" class="w-4 h-4"></i>
                                </button>
                            </form>
                            @if(\App\Support\Currency::current() === 'PKR')
                                <p class="text-xs text-muted-foreground text-center mt-2">Shown in PKR for reference. Payment is processed in USD.</p>
                            @endif
                            <p class="text-xs text-muted-foreground">Opens Stripe checkout in a new tab. On success you can return here; on cancel you'll be redirected back with a message.</p>

                            @if(isset($paymentGateways) && $paymentGateways->count())
                                <div class="pt-2 border-t border-card-border">
                                    <p class="text-sm font-medium mb-3 pt-3">Or pay with</p>
                                    @foreach($paymentGateways as $gateway)
                                        <form method="POST" action="{{ route('payment.start', ['gateway' => $gateway->slug]) }}" class="mb-2">
                                            @csrf
                                            <button type="submit" class="w-full inline-flex items-center justify-center gap-2 px-6 py-3 rounded-xl border border-input bg-background font-semibold hover-elevate transition">
                                                Pay with {{ $gateway->name }}
                                                @if(($gateway->definition()['currency'] ?? 'USD') === 'PKR')
                                                    <span class="text-xs text-muted-foreground">(~Rs {{ number_format($total * (float) \App\Models\Setting::get('usd_to_pkr_rate', 278), 0) }})</span>
                                                @else
                                                    <span class="text-xs text-muted-foreground">({{ \App\Support\Currency::format($total) }})</span>
                                                @endif
                                                <i data-lucide="arrow-up-right" class="w-4 h-4"></i>
                                            </button>
                                        </form>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

