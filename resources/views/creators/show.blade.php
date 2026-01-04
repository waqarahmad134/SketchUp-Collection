@extends('layouts.app')

@section('content')
    <section class="py-24 bg-background relative overflow-hidden">
        <div class="absolute top-0 left-1/2 -translate-x-1/2 w-[800px] h-[400px] bg-gradient-to-b from-cyan-500/5 to-transparent rounded-full blur-3xl"></div>

        <div class="container mx-auto px-4 relative z-10">
            <!-- Profile Header -->
            <div class="glass-card rounded-3xl p-8 mb-8">
                <div class="flex flex-col md:flex-row gap-6 items-center">
                    <div class="relative">
                        <img src="{{ $avatar }}" alt="{{ $user->name }}" class="w-24 h-24 rounded-full border-2 border-border">
                        @if($isOwnProfile)
                            <button class="absolute bottom-0 right-0 w-8 h-8 rounded-full bg-gradient-to-r from-cyan-500 to-violet-500 flex items-center justify-center border-2 border-background hover:scale-110 transition-transform" title="Change Avatar">
                                <i data-lucide="camera" class="w-4 h-4 text-background"></i>
                            </button>
                        @endif
                    </div>
                    <div class="flex-1 text-center md:text-left space-y-3">
                        <div class="flex items-center justify-center md:justify-start gap-3">
                            <h1 class="text-3xl font-bold font-display">{{ $user->name }}</h1>
                            @if($user->role === 'admin')
                                <span class="px-3 py-1 rounded-full bg-red-500/20 text-red-400 text-xs font-bold">Admin</span>
                            @elseif($user->role === 'manager')
                                <span class="px-3 py-1 rounded-full bg-yellow-500/20 text-yellow-400 text-xs font-bold">Manager</span>
                            @elseif($user->role === 'seller')
                                <span class="px-3 py-1 rounded-full bg-green-500/20 text-green-400 text-xs font-bold">Seller</span>
                            @endif
                        </div>
                        @if($user->email)
                            <p class="text-muted-foreground">{{ $user->email }}</p>
                        @endif
                        <div class="flex flex-wrap gap-4 justify-center md:justify-start text-sm text-muted-foreground">
                            @if($isSeller)
                                <span class="inline-flex items-center gap-2">
                                    <i data-lucide="upload" class="w-4 h-4"></i>
                                    {{ $products->count() }} {{ $products->count() === 1 ? 'upload' : 'uploads' }}
                                </span>
                            @endif
                            <span class="inline-flex items-center gap-2">
                                <i data-lucide="calendar" class="w-4 h-4"></i>
                                Joined {{ $user->created_at?->format('M Y') }}
                            </span>
                            @if($isOwnProfile && $orders)
                                <span class="inline-flex items-center gap-2">
                                    <i data-lucide="shopping-cart" class="w-4 h-4"></i>
                                    {{ $orders->total() }} {{ $orders->total() === 1 ? 'order' : 'orders' }}
                                </span>
                            @endif
                        </div>
                    </div>
                    @if(!$isOwnProfile)
                        <div class="flex items-center gap-3">
                            <a href="{{ route('contact') }}" class="px-6 py-3 rounded-xl border border-border hover:border-foreground transition text-sm font-semibold">
                                <i data-lucide="message-circle" class="w-4 h-4 inline mr-2"></i>
                                Message
                            </a>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Tabs Navigation -->
            <div class="glass-card rounded-3xl p-2 mb-6">
                <div class="flex flex-wrap gap-2 overflow-x-auto">
                    <button onclick="showTab('general')" class="tab-btn active px-6 py-3 rounded-xl text-sm font-semibold transition-all" data-tab="general">
                        <i data-lucide="user" class="w-4 h-4 inline mr-2"></i>
                        General Info
                    </button>
                    @if($isOwnProfile)
                        <button onclick="showTab('orders')" class="tab-btn px-6 py-3 rounded-xl text-sm font-semibold transition-all" data-tab="orders">
                            <i data-lucide="shopping-cart" class="w-4 h-4 inline mr-2"></i>
                            Orders
                            @if($orders && $orders->total() > 0)
                                <span class="ml-2 px-2 py-0.5 rounded-full bg-cyan-500/20 text-cyan-400 text-xs">{{ $orders->total() }}</span>
                            @endif
                        </button>
                        <button onclick="showTab('payments')" class="tab-btn px-6 py-3 rounded-xl text-sm font-semibold transition-all" data-tab="payments">
                            <i data-lucide="credit-card" class="w-4 h-4 inline mr-2"></i>
                            Transaction's 
                        </button>
                        <button onclick="showTab('referrals')" class="tab-btn px-6 py-3 rounded-xl text-sm font-semibold transition-all" data-tab="referrals">
                            <i data-lucide="users" class="w-4 h-4 inline mr-2"></i>
                            Referrals
                            @if($referralStats && $referralStats['total_referrals'] > 0)
                                <span class="ml-2 px-2 py-0.5 rounded-full bg-cyan-500/20 text-cyan-400 text-xs">{{ $referralStats['total_referrals'] }}</span>
                            @endif
                        </button>
                    @endif
                    @if($isSeller && $isOwnProfile)
                        <button onclick="showTab('uploads')" class="tab-btn px-6 py-3 rounded-xl text-sm font-semibold transition-all" data-tab="uploads">
                            <i data-lucide="upload" class="w-4 h-4 inline mr-2"></i>
                            My Uploads
                            @if($products->count() > 0)
                                <span class="ml-2 px-2 py-0.5 rounded-full bg-cyan-500/20 text-cyan-400 text-xs">{{ $products->count() }}</span>
                            @endif
                        </button>
                    @endif
                    @if($isOwnProfile)
                        <button onclick="showTab('reviews')" class="tab-btn px-6 py-3 rounded-xl text-sm font-semibold transition-all" data-tab="reviews">
                            <i data-lucide="star" class="w-4 h-4 inline mr-2"></i>
                            Reviews
                        </button>
                        <button onclick="showTab('settings')" class="tab-btn px-6 py-3 rounded-xl text-sm font-semibold transition-all" data-tab="settings">
                            <i data-lucide="settings" class="w-4 h-4 inline mr-2"></i>
                            Settings
                        </button>
                    @elseif($isSeller)
                        <button onclick="showTab('uploads')" class="tab-btn px-6 py-3 rounded-xl text-sm font-semibold transition-all" data-tab="uploads">
                            <i data-lucide="upload" class="w-4 h-4 inline mr-2"></i>
                            Uploads
                            @if($products->count() > 0)
                                <span class="ml-2 px-2 py-0.5 rounded-full bg-cyan-500/20 text-cyan-400 text-xs">{{ $products->count() }}</span>
                            @endif
                        </button>
                    @endif
                </div>
            </div>

            <!-- Tab Content -->
            <div class="space-y-6">
                <!-- General Info Tab -->
                <div id="tab-general" class="tab-content">
                    <div class="glass-card rounded-3xl p-8">
                        <h2 class="text-2xl font-bold font-display mb-6">General Information</h2>
                        <div class="grid md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-semibold mb-2 text-muted-foreground">Full Name</label>
                                <p class="text-lg">{{ $user->name }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-semibold mb-2 text-muted-foreground">Email</label>
                                <p class="text-lg">{{ $user->email }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-semibold mb-2 text-muted-foreground">Role</label>
                                <p class="text-lg capitalize">
                                    @if($user->role === 'admin')
                                        <span class="px-3 py-1 rounded-full bg-red-500/20 text-red-400 text-sm font-bold">Administrator</span>
                                    @elseif($user->role === 'manager')
                                        <span class="px-3 py-1 rounded-full bg-yellow-500/20 text-yellow-400 text-sm font-bold">Manager</span>
                                    @elseif($user->role === 'seller')
                                        <span class="px-3 py-1 rounded-full bg-green-500/20 text-green-400 text-sm font-bold">Seller</span>
                                    @else
                                        <span class="px-3 py-1 rounded-full bg-gray-500/20 text-gray-400 text-sm font-bold">User</span>
                                    @endif
                                </p>
                            </div>
                            <div>
                                <label class="block text-sm font-semibold mb-2 text-muted-foreground">Member Since</label>
                                <p class="text-lg">{{ $user->created_at?->format('F d, Y') }}</p>
                            </div>
                            @if($user->email_verified_at)
                                <div>
                                    <label class="block text-sm font-semibold mb-2 text-muted-foreground">Email Verified</label>
                                    <p class="text-lg text-green-400">
                                        <i data-lucide="check-circle" class="w-5 h-5 inline mr-2"></i>
                                        Verified on {{ $user->email_verified_at->format('M d, Y') }}
                                    </p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Orders Tab -->
                @if($isOwnProfile)
                <div id="tab-orders" class="tab-content hidden">
                    <div class="glass-card rounded-3xl p-8">
                        <h2 class="text-2xl font-bold font-display mb-6">My Orders</h2>
                        @if($orders && $orders->count() > 0)
                            <div class="space-y-4">
                                @foreach($orders as $order)
                                    <div class="border border-border rounded-xl p-6 hover:border-foreground transition-colors">
                                        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-4">
                                            <div>
                                                <h3 class="text-lg font-bold font-display mb-1">Order #{{ $order->order_number }}</h3>
                                                <p class="text-sm text-muted-foreground">{{ $order->created_at->format('M d, Y h:i A') }}</p>
                                            </div>
                                            <div class="flex items-center gap-4">
                                                <div class="text-right">
                                                    <p class="text-2xl font-bold gradient-text">${{ number_format($order->total, 2) }}</p>
                                                    <p class="text-xs text-muted-foreground">{{ $order->currency }}</p>
                                                </div>
                                                <span class="px-4 py-2 rounded-lg text-sm font-semibold
                                                    @if($order->status === 'completed') bg-green-500/20 text-green-400
                                                    @elseif($order->status === 'processing') bg-blue-500/20 text-blue-400
                                                    @elseif($order->status === 'cancelled') bg-red-500/20 text-red-400
                                                    @elseif($order->status === 'refunded') bg-yellow-500/20 text-yellow-400
                                                    @else bg-gray-500/20 text-gray-400
                                                    @endif">
                                                    {{ ucfirst($order->status) }}
                                                </span>
                                            </div>
                                        </div>
                                        @if($order->items->count() > 0)
                                            <div class="space-y-2 mb-4">
                                                @foreach($order->items as $item)
                                                    <div class="p-3 rounded-lg bg-card">
                                                        <div class="flex items-center gap-4">
                                                            @if($item->product)
                                                                <a href="{{ route('bundles.show', $item->product->slug) }}" class="flex items-center gap-3 flex-1">
                                                                    @if($item->product->image_url)
                                                                        <img src="{{ $item->product->image_url }}" alt="{{ $item->product_name }}" class="w-16 h-16 rounded-lg object-cover">
                                                                    @else
                                                                        <div class="w-16 h-16 rounded-lg bg-gradient-to-br from-cyan-500/20 to-violet-500/20 flex items-center justify-center">
                                                                            <i data-lucide="package" class="w-8 h-8 text-cyan-400/50"></i>
                                                                        </div>
                                                                    @endif
                                                                    <div class="flex-1">
                                                                        <p class="font-semibold">{{ $item->product_name }}</p>
                                                                        <p class="text-sm text-muted-foreground">Qty: {{ $item->quantity }} × ${{ number_format($item->price, 2) }}</p>
                                                                    </div>
                                                                    <p class="font-bold">${{ number_format($item->total, 2) }}</p>
                                                                </a>
                                                            @else
                                                                <div class="flex items-center gap-3 flex-1">
                                                                    <div class="w-16 h-16 rounded-lg bg-gradient-to-br from-cyan-500/20 to-violet-500/20 flex items-center justify-center">
                                                                        <i data-lucide="package" class="w-8 h-8 text-cyan-400/50"></i>
                                                                    </div>
                                                                    <div class="flex-1">
                                                                        <p class="font-semibold">{{ $item->product_name }}</p>
                                                                        <p class="text-sm text-muted-foreground">Qty: {{ $item->quantity }} × ${{ number_format($item->price, 2) }}</p>
                                                                    </div>
                                                                    <p class="font-bold">${{ number_format($item->total, 2) }}</p>
                                                                </div>
                                                            @endif
                                                        </div>
                                                        @if($order->status === 'completed' && $item->product && $item->product->is_digital)
                                                            <div class="mt-3 pt-3 border-t border-border">
                                                                <div class="flex flex-wrap gap-2">
                                                                    @if($item->product->download_file)
                                                                        <a href="{{ route('bundles.download', $item->product->slug) }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl border border-border hover:border-foreground transition text-sm font-semibold">
                                                                            <i data-lucide="download" class="w-4 h-4"></i>
                                                                            Download File
                                                                        </a>
                                                                    @endif
                                                                    @if($item->product->download_links && is_array($item->product->download_links) && count($item->product->download_links) > 0)
                                                                        @foreach($item->product->download_links as $link)
                                                                            <a href="{{ $link['url'] ?? '#' }}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl border border-border hover:border-foreground transition text-sm font-semibold">
                                                                                <i data-lucide="external-link" class="w-4 h-4"></i>
                                                                                {{ $link['title'] ?? 'Download Link' }}
                                                                            </a>
                                                                        @endforeach
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        @endif
                                                    </div>
                                                @endforeach
                                            </div>
                                        @endif
                                        <div class="flex items-center justify-between pt-4 border-t border-border">
                                            <div class="text-sm text-muted-foreground">
                                                <p>Payment: <span class="font-semibold text-foreground">{{ ucfirst($order->payment_status) }}</span></p>
                                                @if($order->payment_method)
                                                    <p>Method: <span class="font-semibold text-foreground">{{ $order->payment_method }}</span></p>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            @if($orders->hasPages())
                                <div class="mt-6">
                                    {{ $orders->links() }}
                                </div>
                            @endif
                        @else
                            <div class="text-center py-12">
                                <i data-lucide="shopping-cart" class="w-16 h-16 mx-auto mb-4 text-muted-foreground"></i>
                                <h3 class="text-xl font-bold font-display mb-2">No orders yet</h3>
                                <p class="text-muted-foreground mb-6">Start shopping to see your orders here.</p>
                                <a href="{{ route('bundles.index') }}" class="inline-flex items-center gap-2 px-6 py-3 rounded-xl bg-gradient-to-r from-cyan-500 to-violet-500 text-background font-semibold shadow-lg hover:shadow-xl transition">
                                    Browse Products
                                    <i data-lucide="arrow-right" class="w-4 h-4"></i>
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
                @endif

                <!-- Transaction Tab -->
                @if($isOwnProfile)
                <div id="tab-payments" class="tab-content hidden">
                    <div class="glass-card rounded-3xl p-8 space-y-6">
                        <div>
                            <h2 class="text-2xl font-bold font-display">Payments</h2>
                            <p class="text-sm text-muted-foreground">We don’t store cards. Enter payment details at checkout.</p>
                        </div>

                        <div class="border border-border rounded-2xl p-6">
                            <h3 class="text-xl font-bold font-display mb-4">Transaction History</h3>
                            
                            @php
                                // Combine payment transactions and points transactions, sort by date
                                $allTransactions = collect();
                                
                                if ($transactions && $transactions->count() > 0) {
                                    foreach ($transactions as $tx) {
                                        $allTransactions->push([
                                            'type' => 'payment',
                                            'data' => $tx,
                                            'date' => $tx->created_at,
                                        ]);
                                    }
                                }
                                
                                if ($pointsTransactions && $pointsTransactions->count() > 0) {
                                    foreach ($pointsTransactions as $pt) {
                                        $allTransactions->push([
                                            'type' => 'points',
                                            'data' => $pt,
                                            'date' => $pt->created_at,
                                        ]);
                                    }
                                }
                                
                                $allTransactions = $allTransactions->sortByDesc('date');
                            @endphp
                            
                            @if($allTransactions->count() > 0)
                                <div class="space-y-3">
                                    @foreach($allTransactions as $item)
                                        @if($item['type'] === 'payment')
                                            @php $transaction = $item['data']; @endphp
                                            <div class="flex items-center justify-between p-4 rounded-xl border border-border hover:border-foreground transition-colors">
                                                <div class="flex items-center gap-4 flex-1">
                                                    <div class="w-12 h-12 rounded-lg bg-gradient-to-br from-cyan-500/20 to-violet-500/20 flex items-center justify-center">
                                                        @if($transaction->type === 'payment')
                                                            <i data-lucide="arrow-down-circle" class="w-6 h-6 text-green-400"></i>
                                                        @elseif($transaction->type === 'refund')
                                                            <i data-lucide="arrow-up-circle" class="w-6 h-6 text-red-400"></i>
                                                        @else
                                                            <i data-lucide="dollar-sign" class="w-6 h-6 text-cyan-400"></i>
                                                        @endif
                                                    </div>
                                                    <div class="flex-1">
                                                        <p class="font-semibold">{{ ucfirst($transaction->type) }}
                                                            @if($transaction->order)
                                                                <span class="text-xs text-muted-foreground font-normal">• Order #{{ $transaction->order->order_number }}</span>
                                                            @endif
                                                        </p>
                                                        <p class="text-sm text-muted-foreground">
                                                            {{ $transaction->payment_gateway ?? 'Card' }} • {{ $transaction->created_at->format('M d, Y h:i A') }}
                                                        </p>
                                                        @if($transaction->description)
                                                            <p class="text-xs text-muted-foreground mt-1">{{ $transaction->description }}</p>
                                                        @endif
                                                    </div>
                                                </div>
                                                <div class="text-right">
                                                    <p class="font-bold {{ $transaction->type === 'refund' ? 'text-red-400' : 'text-green-400' }}">
                                                        {{ $transaction->type === 'refund' ? '-' : '+' }}${{ number_format($transaction->amount, 2) }}
                                                    </p>
                                                    <p class="text-xs text-muted-foreground capitalize">{{ $transaction->status }}</p>
                                                    @if($transaction->transaction_id)
                                                        <p class="text-xs text-muted-foreground mt-1">{{ $transaction->transaction_id }}</p>
                                                    @endif
                                                </div>
                                            </div>
                                        @else
                                            @php $pointsTx = $item['data']; @endphp
                                            <div class="flex items-center justify-between p-4 rounded-xl border border-border hover:border-foreground transition-colors bg-yellow-500/5">
                                                <div class="flex items-center gap-4 flex-1">
                                                    <div class="w-12 h-12 rounded-lg bg-gradient-to-br from-yellow-500/20 to-orange-500/20 flex items-center justify-center">
                                                        @if($pointsTx->points > 0)
                                                            <i data-lucide="coins" class="w-6 h-6 text-yellow-400"></i>
                                                        @else
                                                            <i data-lucide="minus-circle" class="w-6 h-6 text-red-400"></i>
                                                        @endif
                                                    </div>
                                                    <div class="flex-1">
                                                        <p class="font-semibold">
                                                            @if($pointsTx->type === 'signup')
                                                                Sign Up Bonus
                                                            @elseif($pointsTx->type === 'referral')
                                                                Referral Commission
                                                            @else
                                                                {{ ucfirst($pointsTx->type) }}
                                                            @endif
                                                        </p>
                                                        <p class="text-sm text-muted-foreground">
                                                            {{ $pointsTx->created_at->format('M d, Y h:i A') }}
                                                        </p>
                                                        @if($pointsTx->description)
                                                            <p class="text-xs text-muted-foreground mt-1">{{ $pointsTx->description }}</p>
                                                        @endif
                                                    </div>
                                                </div>
                                                <div class="text-right">
                                                    <p class="font-bold {{ $pointsTx->points > 0 ? 'text-yellow-400' : 'text-red-400' }}">
                                                        {{ $pointsTx->points > 0 ? '+' : '' }}{{ number_format($pointsTx->points, 0) }} SKP
                                                    </p>
                                                    <p class="text-xs text-muted-foreground">
                                                        Balance: {{ number_format($pointsTx->balance_after ?? 0, 0) }}
                                                    </p>
                                                </div>
                                            </div>
                                        @endif
                                    @endforeach
                                </div>
                            @else
                                <div class="text-center py-12">
                                    <i data-lucide="credit-card" class="w-16 h-16 mx-auto mb-4 text-muted-foreground"></i>
                                    <h3 class="text-xl font-bold font-display mb-2">No transactions yet</h3>
                                    <p class="text-muted-foreground mb-6">Your payments and points transactions will appear here.</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
                @endif

                <!-- Referrals Tab -->
                @if($isOwnProfile)
                <div id="tab-referrals" class="tab-content hidden">
                    <div class="glass-card rounded-3xl p-8 space-y-6">
                        <div>
                            <h2 class="text-2xl font-bold font-display mb-2">Referral Program</h2>
                            <p class="text-sm text-muted-foreground">Earn rewards by referring friends to our platform.</p>
                        </div>

                        <!-- Referral Stats -->
                        @if($referralStats)
                        <div class="grid md:grid-cols-5 gap-4">
                            <div class="border border-border rounded-xl p-4">
                                <p class="text-sm text-muted-foreground mb-1">Total Referrals</p>
                                <p class="text-2xl font-bold gradient-text">{{ $referralStats['total_referrals'] }}</p>
                            </div>
                            <div class="border border-border rounded-xl p-4">
                                <p class="text-sm text-muted-foreground mb-1">Total Earnings</p>
                                <p class="text-2xl font-bold gradient-text">${{ number_format($referralStats['total_earnings'], 2) }}</p>
                                <p class="text-xs text-muted-foreground mt-1">
                                    {{ number_format($referralStats['total_earnings'] * 1000, 0) }} SKP Coins
                                </p>
                            </div>
                            <div class="border border-border rounded-xl p-4">
                                <p class="text-sm text-muted-foreground mb-1">Your Coins</p>
                                <p class="text-2xl font-bold text-yellow-400">{{ number_format($user->getPoints(), 0) }}</p>
                                <p class="text-xs text-muted-foreground mt-1">SKP Points</p>
                            </div>
                            <div class="border border-border rounded-xl p-4">
                                <p class="text-sm text-muted-foreground mb-1">Pending</p>
                                <p class="text-2xl font-bold text-yellow-400">{{ $referralStats['pending_referrals'] }}</p>
                            </div>
                            <div class="border border-border rounded-xl p-4">
                                <p class="text-sm text-muted-foreground mb-1">Rewarded</p>
                                <p class="text-2xl font-bold text-green-400">{{ $referralStats['rewarded_referrals'] }}</p>
                            </div>
                        </div>
                        @endif

                        <!-- Referral Code Section -->
                        <div class="border border-border rounded-2xl p-6">
                            <h3 class="text-xl font-bold font-display mb-4">Your Referral Code</h3>
                            <div class="flex flex-col md:flex-row gap-4 items-start md:items-center">
                                <div class="flex-1">
                                    <div class="flex items-center gap-3 p-4 rounded-xl bg-card border border-border">
                                        <code class="text-2xl font-bold font-mono gradient-text">{{ $user->getReferralCode() }}</code>
                                        <button onclick="copyReferralCode()" class="ml-auto px-4 py-2 rounded-lg border border-border hover:border-foreground transition text-sm font-semibold" title="Copy code">
                                            <i data-lucide="copy" class="w-4 h-4"></i>
                                        </button>
                                    </div>
                                    <p class="text-xs text-muted-foreground mt-2">Share this code with friends to earn rewards!</p>
                                </div>
                            </div>
                            
                            <!-- Referral Link -->
                            <div class="mt-4">
                                <label class="block text-sm font-semibold mb-2">Your Referral Link</label>
                                <div class="flex items-center gap-2">
                                    <input type="text" id="referral-link" value="{{ $user->getReferralUrl() }}" readonly class="flex-1 px-4 py-3 rounded-xl bg-card border border-border text-sm">
                                    <button onclick="copyReferralLink()" class="px-4 py-3 rounded-xl border border-border hover:border-foreground transition text-sm font-semibold">
                                        <i data-lucide="copy" class="w-4 h-4"></i>
                                    </button>
                                    <a href="https://twitter.com/intent/tweet?text={{ urlencode('Join me on ' . config('app.name') . '! Use my referral code: ' . $user->getReferralCode()) }}&url={{ urlencode($user->getReferralUrl()) }}" target="_blank" class="px-4 py-3 rounded-xl border border-border hover:border-foreground transition text-sm font-semibold">
                                        <i data-lucide="share-2" class="w-4 h-4"></i>
                                    </a>
                                </div>
                            </div>
                        </div>

                        <!-- Referral History -->
                        <div class="border border-border rounded-2xl p-6">
                            <h3 class="text-xl font-bold font-display mb-4">Referral History</h3>
                            @if($referrals && $referrals->count() > 0)
                                <div class="space-y-3">
                                    @foreach($referrals as $referral)
                                        <div class="flex items-center justify-between p-4 rounded-xl border border-border hover:border-foreground transition-colors">
                                            <div class="flex items-center gap-4">
                                                <div class="w-12 h-12 rounded-lg bg-gradient-to-br from-cyan-500/20 to-violet-500/20 flex items-center justify-center">
                                                    <i data-lucide="user" class="w-6 h-6 text-cyan-400"></i>
                                                </div>
                                                <div>
                                                    <p class="font-semibold">{{ $referral->referred->name ?? 'Unknown User' }}</p>
                                                    <p class="text-sm text-muted-foreground">
                                                        {{ $referral->referred->email ?? 'N/A' }} • {{ $referral->created_at->format('M d, Y') }}
                                                    </p>
                                                </div>
                                            </div>
                                            <div class="text-right">
                                                <span class="px-3 py-1 rounded-lg text-sm font-semibold
                                                    @if($referral->status === 'rewarded') bg-green-500/20 text-green-400
                                                    @elseif($referral->status === 'completed') bg-blue-500/20 text-blue-400
                                                    @else bg-yellow-500/20 text-yellow-400
                                                    @endif">
                                                    {{ ucfirst($referral->status) }}
                                                </span>
                                                @if($referral->reward_amount > 0)
                                                    <p class="text-sm font-bold text-green-400 mt-1">+${{ number_format($referral->reward_amount, 2) }}</p>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                @if($referrals->hasPages())
                                    <div class="mt-6">
                                        {{ $referrals->links() }}
                                    </div>
                                @endif
                            @else
                                <div class="text-center py-12">
                                    <i data-lucide="users" class="w-16 h-16 mx-auto mb-4 text-muted-foreground"></i>
                                    <h3 class="text-xl font-bold font-display mb-2">No referrals yet</h3>
                                    <p class="text-muted-foreground mb-6">Start sharing your referral code to earn rewards!</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
                @endif

                <!-- Uploads Tab -->
                @if($isSeller)
                <div id="tab-uploads" class="tab-content hidden">
                    <div class="glass-card rounded-3xl p-8">
                        <div class="flex items-center justify-between mb-6">
                            <h2 class="text-2xl font-bold font-display">{{ $isOwnProfile ? 'My Uploads' : 'Uploads' }}</h2>
                            @if($isOwnProfile)
                                <a href="{{ url('/admin/products/create') }}" class="px-6 py-3 rounded-xl bg-gradient-to-r from-cyan-500 to-violet-500 text-background font-semibold shadow-lg hover:shadow-xl transition">
                                    <i data-lucide="plus" class="w-4 h-4 inline mr-2"></i>
                                    Add New Product
                                </a>
                            @endif
                        </div>
                        @if($products->isEmpty())
                            <div class="text-center py-12">
                                <i data-lucide="upload" class="w-16 h-16 mx-auto mb-4 text-muted-foreground"></i>
                                <h3 class="text-xl font-bold font-display mb-2">No uploads yet</h3>
                                <p class="text-muted-foreground mb-6">
                                    @if($isOwnProfile)
                                        Start uploading your first product to get started.
                                    @else
                                        This creator hasn't uploaded any products yet.
                                    @endif
                                </p>
                                @if($isOwnProfile)
                                    <a href="{{ url('/admin/products/create') }}" class="inline-flex items-center gap-2 px-6 py-3 rounded-xl bg-gradient-to-r from-cyan-500 to-violet-500 text-background font-semibold shadow-lg hover:shadow-xl transition">
                                        <i data-lucide="plus" class="w-4 h-4"></i>
                                        Upload Your First Product
                                    </a>
                                @endif
                            </div>
                        @else
                            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
                                @foreach($products as $product)
                                    @php
                                        $discount = $product->discount_percentage ?? ($product->original_price > 0 ? round((1 - $product->price / $product->original_price) * 100) : 0);
                                    @endphp
                                    <a href="{{ route('bundles.show', $product->slug) }}" class="glass-card rounded-3xl overflow-hidden hover-lift group">
                                        <div class="relative overflow-hidden">
                                            @if($product->image_url)
                                                <img src="{{ $product->image_url }}" alt="{{ $product->title }}" class="w-full h-48 object-cover transition-transform duration-700 group-hover:scale-110">
                                            @else
                                                <div class="w-full h-48 bg-gradient-to-br from-cyan-500/20 to-violet-500/20 flex items-center justify-center">
                                                    <i data-lucide="package" class="w-10 h-10 text-cyan-400/60"></i>
                                                </div>
                                            @endif
                                            <div class="absolute inset-0 bg-gradient-to-t from-background/90 via-background/30 to-transparent"></div>
                                            <div class="absolute top-3 left-3 flex gap-2">
                                                @if($product->is_bundle)
                                                    <span class="bg-violet-500/20 text-violet-400 text-xs font-bold px-3 py-1 rounded-full flex items-center gap-1">
                                                        <i data-lucide="package" class="w-3 h-3"></i>
                                                        Bundle
                                                    </span>
                                                @endif
                                                @if($isOwnProfile && !$product->is_active)
                                                    <span class="bg-red-500/20 text-red-400 text-xs font-bold px-3 py-1 rounded-full">
                                                        Inactive
                                                    </span>
                                                @endif
                                            </div>
                                            <div class="absolute bottom-3 left-3 right-3">
                                                <h3 class="text-lg font-bold font-display line-clamp-1">{{ $product->title }}</h3>
                                                <p class="text-xs text-muted-foreground line-clamp-2">{{ $product->description }}</p>
                                            </div>
                                        </div>
                                        <div class="p-4 space-y-3">
                                            <div class="flex items-center justify-between">
                                                <div>
                                                    <span class="text-2xl font-bold gradient-text">${{ number_format($product->price, 2) }}</span>
                                                    @if($product->original_price)
                                                        <span class="text-muted-foreground line-through ml-2 text-sm">${{ number_format($product->original_price, 2) }}</span>
                                                    @endif
                                                </div>
                                                @if($discount > 0)
                                                    <span class="bg-cyan-500/20 text-cyan-400 text-xs font-bold px-3 py-1 rounded-full">
                                                        {{ $discount }}% OFF
                                                    </span>
                                                @endif
                                            </div>
                                            <div class="flex items-center gap-2 text-xs text-muted-foreground">
                                                @if($product->file_size)
                                                    <span>{{ $product->file_size }}</span>
                                                    <span>•</span>
                                                @endif
                                                @if($product->file_count)
                                                    <span>{{ $product->file_count }} files</span>
                                                @endif
                                            </div>
                                            @if($isOwnProfile)
                                                <div class="pt-3 border-t border-border">
                                                    <a href="{{ url('/admin/products/' . $product->id . '/edit') }}" class="w-full inline-flex items-center justify-center gap-2 px-4 py-2 rounded-xl border border-border hover:border-foreground transition text-sm font-semibold">
                                                        <i data-lucide="edit" class="w-4 h-4"></i>
                                                        Edit Product
                                                    </a>
                                                </div>
                                            @endif
                                        </div>
                                    </a>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
                @endif

                <!-- Reviews Tab -->
                @if($isOwnProfile)
                <div id="tab-reviews" class="tab-content hidden">
                    <div class="glass-card rounded-3xl p-8">
                        <h2 class="text-2xl font-bold font-display mb-6">Reviews</h2>
                        <div class="grid md:grid-cols-2 gap-6">
                            <!-- Reviews Given -->
                            <div>
                                <h3 class="text-xl font-bold font-display mb-4">Reviews I've Given</h3>
                                @if($reviewsGiven->count() > 0)
                                    <div class="space-y-4">
                                        @foreach($reviewsGiven as $review)
                                            <div class="border border-border rounded-xl p-4">
                                                <div class="flex items-start justify-between mb-2">
                                                    <a href="{{ route('bundles.show', $review->product->slug) }}" class="font-semibold hover:text-cyan-400 transition">
                                                        {{ $review->product->title }}
                                                    </a>
                                                    <div class="flex items-center gap-1">
                                                        @for($i = 1; $i <= 5; $i++)
                                                            <i data-lucide="star" class="w-4 h-4 {{ $i <= $review->rating ? 'text-yellow-400 fill-yellow-400' : 'text-muted-foreground' }}"></i>
                                                        @endfor
                                                    </div>
                                                </div>
                                                @if($review->comment)
                                                    <p class="text-sm text-muted-foreground mb-2">{{ $review->comment }}</p>
                                                @endif
                                                <p class="text-xs text-muted-foreground">{{ $review->created_at->format('M d, Y') }}</p>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <div class="text-center py-8 border border-border rounded-xl">
                                        <i data-lucide="star" class="w-12 h-12 mx-auto mb-3 text-muted-foreground"></i>
                                        <p class="text-muted-foreground">You haven't reviewed any products yet.</p>
                                    </div>
                                @endif
                            </div>

                            <!-- Reviews Received (for sellers) -->
                            @if($isSeller)
                            <div>
                                <h3 class="text-xl font-bold font-display mb-4">Reviews I've Received</h3>
                                @if($reviewsReceived->count() > 0)
                                    <div class="space-y-4">
                                        @foreach($reviewsReceived as $review)
                                            <div class="border border-border rounded-xl p-4">
                                                <div class="flex items-start justify-between mb-2">
                                                    <div>
                                                        <p class="font-semibold">{{ $review->user->name ?? 'Anonymous' }}</p>
                                                        <a href="{{ route('bundles.show', $review->product->slug) }}" class="text-sm text-muted-foreground hover:text-cyan-400 transition">
                                                            {{ $review->product->title }}
                                                        </a>
                                                    </div>
                                                    <div class="flex items-center gap-1">
                                                        @for($i = 1; $i <= 5; $i++)
                                                            <i data-lucide="star" class="w-4 h-4 {{ $i <= $review->rating ? 'text-yellow-400 fill-yellow-400' : 'text-muted-foreground' }}"></i>
                                                        @endfor
                                                    </div>
                                                </div>
                                                @if($review->comment)
                                                    <p class="text-sm text-muted-foreground mb-2">{{ $review->comment }}</p>
                                                @endif
                                                <p class="text-xs text-muted-foreground">{{ $review->created_at->format('M d, Y') }}</p>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <div class="text-center py-8 border border-border rounded-xl">
                                        <i data-lucide="star" class="w-12 h-12 mx-auto mb-3 text-muted-foreground"></i>
                                        <p class="text-muted-foreground">No reviews received yet.</p>
                                    </div>
                                @endif
                            </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Settings Tab -->
                <div id="tab-settings" class="tab-content hidden">
                    <div class="glass-card rounded-3xl p-8">
                        <h2 class="text-2xl font-bold font-display mb-6">Account Settings</h2>
                        <div class="space-y-6">
                            <!-- Profile Settings -->
                            <div class="border border-border rounded-xl p-6">
                                <h3 class="text-xl font-bold font-display mb-4">Profile Information</h3>
                                <form class="space-y-4">
                                    @csrf
                                    <div>
                                        <label class="block text-sm font-semibold mb-2">Full Name</label>
                                        <input type="text" value="{{ $user->name }}" class="w-full px-4 py-3 rounded-xl bg-card border border-border focus:border-cyan-500 focus:ring-2 focus:ring-cyan-500/30 outline-none" disabled>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-semibold mb-2">Email</label>
                                        <input type="email" value="{{ $user->email }}" class="w-full px-4 py-3 rounded-xl bg-card border border-border focus:border-cyan-500 focus:ring-2 focus:ring-cyan-500/30 outline-none" disabled>
                                    </div>
                                    <p class="text-xs text-muted-foreground">To change your name or email, please contact support.</p>
                                </form>
                            </div>

                            <!-- Password Settings -->
                            <div class="border border-border rounded-xl p-6">
                                <h3 class="text-xl font-bold font-display mb-4">Change Password</h3>
                                <form class="space-y-4">
                                    @csrf
                                    <div>
                                        <label class="block text-sm font-semibold mb-2">Current Password</label>
                                        <input type="password" class="w-full px-4 py-3 rounded-xl bg-card border border-border focus:border-cyan-500 focus:ring-2 focus:ring-cyan-500/30 outline-none" placeholder="Enter current password">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-semibold mb-2">New Password</label>
                                        <input type="password" class="w-full px-4 py-3 rounded-xl bg-card border border-border focus:border-cyan-500 focus:ring-2 focus:ring-cyan-500/30 outline-none" placeholder="Enter new password">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-semibold mb-2">Confirm New Password</label>
                                        <input type="password" class="w-full px-4 py-3 rounded-xl bg-card border border-border focus:border-cyan-500 focus:ring-2 focus:ring-cyan-500/30 outline-none" placeholder="Confirm new password">
                                    </div>
                                    <button type="submit" class="px-6 py-3 rounded-xl bg-gradient-to-r from-cyan-500 to-violet-500 text-background font-semibold shadow-lg hover:shadow-xl transition">
                                        Update Password
                                    </button>
                                </form>
                            </div>

                            <!-- Notification Settings -->
                            <div class="border border-border rounded-xl p-6">
                                <h3 class="text-xl font-bold font-display mb-4">Notification Preferences</h3>
                                <div class="space-y-3">
                                    <label class="flex items-center gap-3 cursor-pointer">
                                        <input type="checkbox" checked class="w-4 h-4 text-cyan-500 border-border rounded focus:ring-cyan-500">
                                        <span>Email notifications for new orders</span>
                                    </label>
                                    <label class="flex items-center gap-3 cursor-pointer">
                                        <input type="checkbox" checked class="w-4 h-4 text-cyan-500 border-border rounded focus:ring-cyan-500">
                                        <span>Email notifications for product reviews</span>
                                    </label>
                                    <label class="flex items-center gap-3 cursor-pointer">
                                        <input type="checkbox" class="w-4 h-4 text-cyan-500 border-border rounded focus:ring-cyan-500">
                                        <span>Marketing emails and updates</span>
                                    </label>
                                </div>
                            </div>

                            <!-- Danger Zone -->
                            <div class="border border-red-500/30 rounded-xl p-6 bg-red-500/5">
                                <h3 class="text-xl font-bold font-display mb-4 text-red-400">Danger Zone</h3>
                                <p class="text-sm text-muted-foreground mb-4">Once you delete your account, there is no going back. Please be certain.</p>
                                <button type="button" class="px-6 py-3 rounded-xl border border-red-500/50 text-red-400 font-semibold hover:bg-red-500/10 transition">
                                    Delete Account
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </section>

    <script>
        function showTab(tabName) {
            // Hide all tab contents
            document.querySelectorAll('.tab-content').forEach(content => {
                content.classList.add('hidden');
            });
            
            // Remove active class from all buttons
            document.querySelectorAll('.tab-btn').forEach(btn => {
                btn.classList.remove('active', 'bg-gradient-to-r', 'from-cyan-500', 'to-violet-500', 'text-background');
                btn.classList.add('text-muted-foreground', 'hover:text-foreground');
            });
            
            // Show selected tab content
            const selectedTab = document.getElementById('tab-' + tabName);
            if (selectedTab) {
                selectedTab.classList.remove('hidden');
            }
            
            // Add active class to selected button
            const selectedBtn = document.querySelector(`[data-tab="${tabName}"]`);
            if (selectedBtn) {
                selectedBtn.classList.add('active', 'bg-gradient-to-r', 'from-cyan-500', 'to-violet-500', 'text-background');
                selectedBtn.classList.remove('text-muted-foreground', 'hover:text-foreground');
            }
            
            // Reinitialize Lucide icons
            if (typeof lucide !== 'undefined') {
                lucide.createIcons();
            }
        }

        // Initialize on page load
        document.addEventListener('DOMContentLoaded', () => {
            // Show first tab by default
            showTab('general');
        });

        // Copy referral code
        function copyReferralCode() {
            const code = '{{ $user->getReferralCode() }}';
            navigator.clipboard.writeText(code).then(() => {
                const btn = event.target.closest('button');
                const originalHTML = btn.innerHTML;
                btn.innerHTML = '<i data-lucide="check" class="w-4 h-4"></i>';
                btn.classList.add('bg-green-500/20', 'border-green-500');
                setTimeout(() => {
                    btn.innerHTML = originalHTML;
                    btn.classList.remove('bg-green-500/20', 'border-green-500');
                    if (typeof lucide !== 'undefined') {
                        lucide.createIcons();
                    }
                }, 2000);
            });
        }

        // Copy referral link
        function copyReferralLink() {
            const link = document.getElementById('referral-link').value;
            navigator.clipboard.writeText(link).then(() => {
                const btn = event.target.closest('button');
                const originalHTML = btn.innerHTML;
                btn.innerHTML = '<i data-lucide="check" class="w-4 h-4"></i>';
                btn.classList.add('bg-green-500/20', 'border-green-500');
                setTimeout(() => {
                    btn.innerHTML = originalHTML;
                    btn.classList.remove('bg-green-500/20', 'border-green-500');
                    if (typeof lucide !== 'undefined') {
                        lucide.createIcons();
                    }
                }, 2000);
            });
        }
    </script>
@endsection
