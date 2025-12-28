@extends('layouts.app')

@section('content')
    <section class="py-24 mesh-gradient relative overflow-hidden min-h-[calc(100vh-5rem)] flex items-center">
        <div class="absolute top-0 left-1/2 -translate-x-1/2 w-[800px] h-[400px] bg-gradient-to-b from-cyan-500/10 to-transparent rounded-full blur-3xl"></div>

        <div class="container mx-auto px-4 relative z-10">
            <div class="max-w-md mx-auto">
                <div class="glass-card rounded-3xl p-8">
                    <div class="text-center mb-8">
                        <div class="w-16 h-16 mx-auto mb-4 rounded-2xl bg-gradient-to-br from-cyan-500/20 to-violet-500/20 flex items-center justify-center">
                            <i data-lucide="log-in" class="w-8 h-8 text-cyan-400"></i>
                        </div>
                        <h1 class="text-3xl font-bold font-display mb-2">Welcome Back</h1>
                        <p class="text-muted-foreground">Sign in to access your assets</p>
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

                    <form class="space-y-6" method="POST" action="{{ route('login.submit') }}">
                        @csrf
                        <div class="space-y-2">
                            <label for="email" class="font-medium">Email</label>
                            <div class="relative">
                                <i data-lucide="mail" class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-muted-foreground"></i>
                                <input
                                    id="email"
                                    name="email"
                                    type="email"
                                    value="{{ old('email') }}"
                                    required
                                    class="w-full px-10 py-3 rounded-xl bg-card border border-border focus:border-cyan-500 focus:ring-2 focus:ring-cyan-500/30 outline-none"
                                    placeholder="your@email.com"
                                >
                            </div>
                        </div>

                        <div class="space-y-2">
                            <div class="flex items-center justify-between">
                                <label for="password" class="font-medium">Password</label>
                                <a href="#" class="text-sm text-cyan-400 hover:underline">Forgot password?</a>
                            </div>
                            <div class="relative">
                                <i data-lucide="lock" class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-muted-foreground"></i>
                                <input
                                    id="password"
                                    name="password"
                                    type="password"
                                    required
                                    class="w-full px-10 py-3 rounded-xl bg-card border border-border focus:border-cyan-500 focus:ring-2 focus:ring-cyan-500/30 outline-none"
                                    placeholder="••••••••"
                                >
                            </div>
                        </div>

                        <button type="submit" class="w-full px-4 py-3 rounded-xl bg-gradient-to-r from-cyan-500 to-violet-500 text-background font-semibold shadow-lg hover:shadow-xl transition">
                            Sign In
                        </button>
                    </form>

                    <div class="mt-6 text-center">
                        <p class="text-sm text-muted-foreground">
                            Don't have an account?
                            <a href="{{ route('register') }}" class="text-cyan-400 font-semibold hover:underline">Sign up</a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

