@extends('layouts.app')

@section('content')
    <section class="py-16">
        <div class="container mx-auto px-4">
            <div class="max-w-5xl mx-auto">
                <h1 class="text-3xl md:text-4xl font-bold font-display mb-2">My <span class="gradient-text">Account</span></h1>
                <p class="text-muted-foreground mb-8">Welcome back, {{ $user->name }}. Your orders and downloads live here.</p>

                @if(session('status'))
                    <div class="glass-card rounded-2xl p-4 mb-6 border-green-500/30 text-green-400 text-sm">
                        {{ session('status') }}
                    </div>
                @endif

                @if($user->must_set_password)
                    <div class="glass-card rounded-2xl p-6 mb-6 border-yellow-500/30">
                        <h2 class="font-semibold mb-1 flex items-center gap-2">
                            <i data-lucide="key-round" class="w-5 h-5 text-yellow-400"></i>
                            Set your account password
                        </h2>
                        <p class="text-sm text-muted-foreground mb-4">Your account was created at checkout. Set a password to sign in directly next time.</p>
                        <form method="POST" action="{{ route('account.password') }}" class="grid md:grid-cols-3 gap-3">
                            @csrf
                            <input type="password" name="password" required minlength="8" placeholder="New password"
                                class="flex h-10 rounded-md border border-input bg-background px-3 py-2 text-sm">
                            <input type="password" name="password_confirmation" required minlength="8" placeholder="Confirm password"
                                class="flex h-10 rounded-md border border-input bg-background px-3 py-2 text-sm">
                            <button type="submit" class="rounded-md text-sm font-medium min-h-10 px-4 py-2 bg-primary text-primary-foreground border border-primary-border hover-elevate">
                                Set Password
                            </button>
                        </form>
                        @error('password')
                            <p class="mt-2 text-sm text-destructive">{{ $message }}</p>
                        @enderror
                    </div>
                @endif

                @if($orders->count() > 0)
                    <div class="space-y-4">
                        @foreach($orders as $order)
                            <a href="{{ route('account.order', $order) }}" class="glass-card rounded-2xl p-5 flex flex-wrap items-center gap-4 hover:border-cyan-500/50 transition-all">
                                <div class="flex-1 min-w-[200px]">
                                    <p class="font-semibold">{{ $order->order_number }}</p>
                                    <p class="text-sm text-muted-foreground">{{ $order->created_at->format('M d, Y') }} - {{ $order->items->count() }} item(s)</p>
                                </div>
                                <span class="px-3 py-1 rounded-full text-xs font-bold {{ $order->status === 'completed' ? 'bg-green-500/20 text-green-400' : 'bg-yellow-500/20 text-yellow-400' }}">
                                    {{ ucfirst($order->status) }}
                                </span>
                                <span class="text-xl font-bold gradient-text">${{ number_format($order->total, 2) }}</span>
                                <span class="inline-flex items-center gap-1 text-sm text-cyan-400 font-medium">
                                    View <i data-lucide="arrow-right" class="w-4 h-4"></i>
                                </span>
                            </a>
                        @endforeach
                    </div>

                    <div class="mt-8">
                        {{ $orders->links() }}
                    </div>
                @else
                    <div class="glass-card rounded-3xl p-12 text-center">
                        <i data-lucide="shopping-bag" class="w-16 h-16 text-muted-foreground mx-auto mb-4"></i>
                        <h2 class="text-2xl font-bold mb-2">No orders yet</h2>
                        <p class="text-muted-foreground mb-6">Browse our bundles and grab your first collection.</p>
                        <a href="{{ route('bundles.index') }}" class="inline-flex items-center gap-2 px-8 py-4 rounded-xl bg-gradient-to-r from-cyan-500 to-violet-500 text-background font-semibold shadow-lg hover:shadow-xl transition">
                            Browse Bundles <i data-lucide="arrow-right" class="w-5 h-5"></i>
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </section>
@endsection
