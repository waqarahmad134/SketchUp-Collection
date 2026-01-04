@extends('layouts.app')

@section('content')
    <section class="py-24 mesh-gradient relative overflow-hidden min-h-[calc(100vh-5rem)] flex items-center">
        <div class="absolute top-0 left-1/2 -translate-x-1/2 w-[800px] h-[400px] bg-gradient-to-b from-violet-500/10 to-transparent rounded-full blur-3xl"></div>

        <div class="container mx-auto px-4 relative z-10">
            <div class="max-w-md mx-auto">
                <div class="glass-card rounded-3xl p-8">
                    <div class="text-center mb-8">
                        <div class="w-16 h-16 mx-auto mb-4 rounded-2xl bg-gradient-to-br from-cyan-500/20 to-violet-500/20 flex items-center justify-center">
                            <i data-lucide="user-plus" class="w-8 h-8 text-cyan-400"></i>
                        </div>
                        <h1 class="text-3xl font-bold font-display mb-2">Create Account</h1>
                        <p class="text-muted-foreground">Get started with free assets today</p>
                    </div>

                    @if ($errors->any())
                        <div class="mb-4 rounded-xl border border-red-500/40 bg-red-500/10 text-red-100 p-3 text-sm">
                            {{ $errors->first() }}
                        </div>
                    @endif

                    @if (session('status'))
                        <div class="mb-4 rounded-xl border border-cyan-500/40 bg-cyan-500/10 text-cyan-100 p-3 text-sm">
                            {{ session('status') }}
                        </div>
                    @endif

                    @if($referrer)
                        <div class="mb-4 rounded-xl border border-violet-500/40 bg-violet-500/10 text-violet-100 p-3 text-sm">
                            <i data-lucide="gift" class="w-4 h-4 inline mr-2"></i>
                            You were referred by <strong>{{ $referrer->name }}</strong>! 🎉
                        </div>
                    @endif

                    @if($claimDailyBonus ?? false)
                        <div class="mb-4 rounded-xl border border-yellow-500/40 bg-yellow-500/10 text-yellow-100 p-3 text-sm">
                            <i data-lucide="gift" class="w-4 h-4 inline mr-2"></i>
                            Sign up to claim your daily bonus!
                        </div>
                    @endif

                    <form class="space-y-6" method="POST" action="{{ route('register.submit') }}">
                        @csrf
                        @if($claimDailyBonus ?? false)
                            <input type="hidden" name="claim_daily_bonus" value="1">
                        @endif
                        <div class="space-y-2">
                            <label for="name" class="font-medium">Full Name</label>
                            <div class="relative">
                                <i data-lucide="user" class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-muted-foreground pointer-events-none z-10"></i>
                                <input
                                    id="name"
                                    name="name"
                                    type="text"
                                    value="{{ old('name') }}"
                                    required
                                    class="w-full pl-14 pr-4 py-3 rounded-xl bg-card border border-border focus:border-cyan-500 focus:ring-2 focus:ring-cyan-500/30 outline-none"
                                    placeholder="John Doe"
                                >
                            </div>
                        </div>

                        <div class="space-y-2">
                            <label for="email" class="font-medium">Email</label>
                            <div class="relative">
                                <i data-lucide="mail" class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-muted-foreground pointer-events-none z-10"></i>
                                <input
                                    id="email"
                                    name="email"
                                    type="email"
                                    value="{{ old('email') }}"
                                    required
                                    class="w-full pl-14 pr-4 py-3 rounded-xl bg-card border border-border focus:border-cyan-500 focus:ring-2 focus:ring-cyan-500/30 outline-none"
                                    placeholder="your@email.com"
                                >
                            </div>
                        </div>

                        <div class="space-y-2">
                            <label for="password" class="font-medium">Password</label>
                            <div class="relative">
                                <i data-lucide="lock" class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-muted-foreground pointer-events-none z-10"></i>
                                <input
                                    id="password"
                                    name="password"
                                    type="password"
                                    required
                                    minlength="8"
                                    class="w-full pl-14 pr-4 py-3 rounded-xl bg-card border border-border focus:border-cyan-500 focus:ring-2 focus:ring-cyan-500/30 outline-none"
                                    placeholder="••••••••"
                                >
                            </div>
                        </div>

                        <div class="space-y-2">
                            <label for="password_confirmation" class="font-medium">Confirm Password</label>
                            <div class="relative">
                                <i data-lucide="lock" class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-muted-foreground pointer-events-none z-10"></i>
                                <input
                                    id="password_confirmation"
                                    name="password_confirmation"
                                    type="password"
                                    required
                                    minlength="8"
                                    class="w-full pl-14 pr-4 py-3 rounded-xl bg-card border border-border focus:border-cyan-500 focus:ring-2 focus:ring-cyan-500/30 outline-none"
                                    placeholder="••••••••"
                                >
                            </div>
                        </div>

                        <div class="space-y-2">
                            <label for="referral_code" class="font-medium">
                                Referral Code
                                <span class="text-xs text-muted-foreground font-normal">(Optional)</span>
                            </label>
                            <div class="relative">
                                <i data-lucide="gift" class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-muted-foreground pointer-events-none z-10"></i>
                                <input
                                    id="referral_code"
                                    name="referral_code"
                                    type="text"
                                    value="{{ old('referral_code', $referralCode ?? '') }}"
                                    class="w-full pl-14 pr-4 py-3 rounded-xl bg-card border border-border focus:border-cyan-500 focus:ring-2 focus:ring-cyan-500/30 outline-none uppercase"
                                    placeholder="Enter referral code"
                                    style="text-transform: uppercase;"
                                >
                            </div>
                            @if($errors->has('referral_code'))
                                <p class="text-sm text-red-400 mt-1">{{ $errors->first('referral_code') }}</p>
                            @endif
                            @if($referralCode && !$errors->has('referral_code'))
                                <p class="text-xs text-muted-foreground mt-1">
                                    <i data-lucide="info" class="w-3 h-3 inline"></i>
                                    You can change the referral code above if needed.
                                </p>
                            @endif
                        </div>

                        <button type="submit" class="w-full px-4 py-3 rounded-xl bg-gradient-to-r from-cyan-500 to-violet-500 text-background font-semibold shadow-lg hover:shadow-xl transition">
                            Create Account
                        </button>
                    </form>

                    <div class="mt-6 text-center">
                        <p class="text-sm text-muted-foreground">
                            Already have an account?
                            <a href="{{ route('login') }}" class="text-cyan-400 font-semibold hover:underline">Sign in</a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <script>
        // Auto-uppercase referral code input
        document.addEventListener('DOMContentLoaded', function() {
            const referralInput = document.getElementById('referral_code');
            if (referralInput) {
                referralInput.addEventListener('input', function(e) {
                    e.target.value = e.target.value.toUpperCase().replace(/[^A-Z0-9]/g, '');
                });
            }
        });
    </script>
@endsection

