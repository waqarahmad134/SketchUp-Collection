<nav class="fixed top-0 left-0 right-0 z-50 glass-card border-b border-border backdrop-blur">
    <div class="container mx-auto px-4">
        <div class="flex items-center justify-between h-20">
            <a href="{{ url('/') }}" class="flex items-center gap-2 group">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-cyan-500 to-violet-500 flex items-center justify-center group-hover:scale-110 transition-transform">
                    <i data-lucide="box" class="w-5 h-5 text-background"></i>
                </div>
                <span class="text-xl font-bold font-display">
                    <span class="gradient-text">SketchUp</span> Collection
                </span>
            </a>

            <div class="hidden md:flex items-center gap-8">
                @foreach(($navMenus ?? []) as $link)
                    @php
                        $href = $link->url ?? '#';
                        if (!empty($link->route) && \Illuminate\Support\Facades\Route::has($link->route)) {
                            $href = route($link->route);
                        }
                    @endphp
                    <a
                        href="{{ $href }}"
                        target="{{ $link->target ?? '_self' }}"
                        class="text-muted-foreground hover:text-foreground transition-colors relative group {{ $link->css_class }}"
                    >
                        {{ $link->label }}
                        <span class="absolute -bottom-1 left-0 w-0 h-0.5 bg-gradient-to-r from-cyan-500 to-violet-500 transition-all group-hover:w-full"></span>
                    </a>
                @endforeach
            </div>

            <div class="hidden md:flex items-center gap-4">
                @auth
                    <div class="flex items-center gap-2 px-3 py-2 rounded-xl border border-border bg-card">
                        <i data-lucide="coins" class="w-4 h-4 text-yellow-400"></i>
                        <span class="text-sm font-semibold gradient-text">{{ number_format($userPoints ?? 0) }}</span>
                        <span class="text-xs text-muted-foreground">SKP</span>
                    </div>
                    
                    <button onclick="openDailyBonusModal()" class="relative inline-flex items-center justify-center w-11 h-11 rounded-xl border border-border hover:border-yellow-500 transition-colors group" aria-label="Daily Bonus" title="Claim Daily Bonus">
                        <i data-lucide="gift" class="w-5 h-5 text-yellow-400 group-hover:scale-110 transition-transform"></i>
                    </button>
                @endauth
                
                <a href="{{ route('cart.show') }}" class="relative inline-flex items-center justify-center w-11 h-11 rounded-xl border border-border hover:border-foreground transition-colors" aria-label="Cart">
                    <i data-lucide="shopping-cart" class="w-5 h-5"></i>
                    <span data-cart-count class="absolute -top-2 -right-2 min-w-[20px] h-5 px-1 rounded-full bg-gradient-to-r from-cyan-500 to-violet-500 text-[11px] font-bold text-background flex items-center justify-center">
                        {{ $cartQuantity ?? 0 }}
                    </span>
                </a>

                @auth
                    <!-- User Menu -->
                    <div class="relative group">
                        <button class="flex items-center gap-2 px-4 py-2 rounded-xl border border-border text-sm font-medium hover:border-foreground transition-colors">
                            <div class="w-8 h-8 rounded-full bg-gradient-to-br from-cyan-500 to-violet-500 flex items-center justify-center">
                                <span class="text-xs font-bold text-background">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</span>
                            </div>
                            <span>{{ auth()->user()->name }}</span>
                            <i data-lucide="chevron-down" class="w-4 h-4"></i>
                        </button>
                        <div class="absolute right-0 top-full mt-2 w-48 glass-card rounded-xl p-2 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 z-50">
                            <a href="{{ route('profile') }}" class="block px-4 py-2 rounded-lg hover:bg-card transition-colors text-sm">
                                <div class="flex items-center gap-2">
                                    <i data-lucide="user" class="w-4 h-4"></i>
                                    <span>My Profile</span>
                                </div>
                            </a>
                            @if(auth()->user()->canSell())
                                <a href="{{ url('/admin') }}" class="block px-4 py-2 rounded-lg hover:bg-card transition-colors text-sm">
                                    <div class="flex items-center gap-2">
                                        <i data-lucide="settings" class="w-4 h-4"></i>
                                        <span>Dashboard</span>
                                    </div>
                                </a>
                            @endif
                            <form method="POST" action="{{ route('logout') }}" class="mt-2">
                                @csrf
                                <button type="submit" class="w-full text-left px-4 py-2 rounded-lg hover:bg-card transition-colors text-sm text-red-400">
                                    <div class="flex items-center gap-2">
                                        <i data-lucide="log-out" class="w-4 h-4"></i>
                                        <span>Logout</span>
                                    </div>
                                </button>
                            </form>
                        </div>
                    </div>
                @else
                    <a href="{{ route('login') }}" class="px-4 py-2 rounded-xl border border-border text-sm font-medium hover:border-foreground transition-colors">Login</a>
                    <a href="{{ route('register') }}" class="px-4 py-2 rounded-xl bg-gradient-to-r from-cyan-500 to-violet-500 text-background text-sm font-semibold shadow-lg hover:shadow-xl transition">Get Started</a>
                @endauth
            </div>

            <button class="md:hidden p-2" data-mobile-toggle aria-label="Toggle navigation">
                <i data-lucide="menu" class="w-6 h-6"></i>
            </button>
        </div>

        <div class="md:hidden py-4 border-t border-border hidden" data-mobile-menu>
            <div class="flex flex-col gap-4">
                @foreach(($navMenus ?? []) as $link)
                    @php
                        $href = $link->url ?? '#';
                        if (!empty($link->route) && \Illuminate\Support\Facades\Route::has($link->route)) {
                            $href = route($link->route);
                        }
                    @endphp
                    <a
                        href="{{ $href }}"
                        target="{{ $link->target ?? '_self' }}"
                        class="text-muted-foreground hover:text-foreground transition-colors py-2 {{ $link->css_class }}"
                    >
                        {{ $link->label }}
                    </a>
                @endforeach

                @auth
                    <div class="flex items-center gap-3 px-4 py-3 rounded-xl border border-border bg-card">
                        <i data-lucide="coins" class="w-5 h-5 text-yellow-400"></i>
                        <div>
                            <span class="font-semibold gradient-text">{{ number_format($userPoints ?? 0) }}</span>
                            <span class="text-xs text-muted-foreground ml-1">SKP</span>
                        </div>
                    </div>
                    
                    <button onclick="openDailyBonusModal()" class="w-full flex items-center gap-3 px-4 py-3 rounded-xl border border-border hover:border-yellow-500 transition-colors" aria-label="Daily Bonus">
                        <i data-lucide="gift" class="w-5 h-5 text-yellow-400"></i>
                        <span class="font-medium">Claim Bonus</span>
                    </button>
                @endauth

                <a href="{{ route('cart.show') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl border border-border hover:border-foreground transition-colors">
                    <div class="relative">
                        <i data-lucide="shopping-cart" class="w-5 h-5"></i>
                        <span data-cart-count class="absolute -top-2 -right-2 min-w-[20px] h-5 px-1 rounded-full bg-gradient-to-r from-cyan-500 to-violet-500 text-[11px] font-bold text-background flex items-center justify-center">
                            {{ $cartQuantity ?? 0 }}
                        </span>
                    </div>
                    <span class="font-medium">Cart</span>
                </a>

                <div class="flex flex-col gap-2 pt-4 border-t border-border">
                    @auth
                        <a href="{{ route('profile') }}" class="w-full px-4 py-2 rounded-xl border border-border text-sm font-medium text-center hover:border-foreground transition-colors">
                            <div class="flex items-center justify-center gap-2">
                                <i data-lucide="user" class="w-4 h-4"></i>
                                <span>My Profile</span>
                            </div>
                        </a>
                        @if(auth()->user()->canSell())
                            <a href="{{ url('/admin') }}" class="w-full px-4 py-2 rounded-xl border border-border text-sm font-medium text-center hover:border-foreground transition-colors">
                                <div class="flex items-center justify-center gap-2">
                                    <i data-lucide="settings" class="w-4 h-4"></i>
                                    <span>Dashboard</span>
                                </div>
                            </a>
                        @endif
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="w-full px-4 py-2 rounded-xl bg-gradient-to-r from-red-500 to-red-600 text-background text-sm font-semibold text-center shadow-lg hover:shadow-xl transition">
                                <div class="flex items-center justify-center gap-2">
                                    <i data-lucide="log-out" class="w-4 h-4"></i>
                                    <span>Logout</span>
                                </div>
                            </button>
                        </form>
                    @else
                        <a href="{{ route('login') }}" class="w-full px-4 py-2 rounded-xl border border-border text-sm font-medium text-center hover:border-foreground transition-colors">
                            Login
                        </a>
                        <a href="{{ route('register') }}" class="w-full px-4 py-2 rounded-xl bg-gradient-to-r from-cyan-500 to-violet-500 text-background text-sm font-semibold text-center shadow-lg hover:shadow-xl transition">
                            Get Started
                        </a>
                    @endauth
                </div>
            </div>
        </div>
    </div>
</nav>

