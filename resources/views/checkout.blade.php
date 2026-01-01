@extends('layouts.app')

@section('content')
    <section class="py-16 bg-background">
        <div class="container mx-auto px-4">
            <div class="max-w-3xl mx-auto glass-card rounded-3xl p-8 space-y-6">
                <div>
                    <h1 class="text-3xl font-bold font-display">Checkout</h1>
                    <p class="text-sm text-muted-foreground">Enter your card securely to complete the purchase. We do not store card details.</p>
                </div>

                <div class="border border-border rounded-2xl p-6">
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
                    <div class="space-y-4">
                        <form method="GET" action="{{ route('checkout.stripe.start') }}" target="_blank">
                            <button type="submit" class="w-full inline-flex items-center justify-center gap-2 px-6 py-3 rounded-xl bg-gradient-to-r from-cyan-500 to-violet-500 text-background font-semibold shadow-lg hover:shadow-xl transition">
                                Pay with Stripe
                                <i data-lucide="arrow-up-right" class="w-4 h-4"></i>
                            </button>
                        </form>
                        <p class="text-xs text-muted-foreground">Opens Stripe checkout in a new tab. On success you can return here; on cancel you’ll be redirected back with a message.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

