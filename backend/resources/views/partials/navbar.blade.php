<nav class="fixed top-0 left-0 right-0 z-50 glass-card border-b border-border backdrop-blur">
    <div class="container mx-auto px-4">
        <div class="flex items-center justify-between h-20">
            <a href="{{ url('/') }}" class="flex items-center gap-2 group">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-cyan-500 to-violet-500 flex items-center justify-center group-hover:scale-110 transition-transform">
                    <i data-lucide="box" class="w-5 h-5 text-background"></i>
                </div>
                <span class="text-xl font-bold font-display">
                    <span class="gradient-text">3D</span>AssetHub
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
                <a href="{{ url('/login') }}" class="px-4 py-2 rounded-xl border border-border text-sm font-medium hover:border-foreground transition-colors">Login</a>
                <a href="{{ url('/signup') }}" class="px-4 py-2 rounded-xl bg-gradient-to-r from-cyan-500 to-violet-500 text-background text-sm font-semibold shadow-lg hover:shadow-xl transition">Get Started</a>
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
                <div class="flex flex-col gap-2 pt-4 border-t border-border">
                    <a href="{{ url('/login') }}" class="w-full px-4 py-2 rounded-xl border border-border text-sm font-medium text-center hover:border-foreground transition-colors">
                        Login
                    </a>
                    <a href="{{ url('/signup') }}" class="w-full px-4 py-2 rounded-xl bg-gradient-to-r from-cyan-500 to-violet-500 text-background text-sm font-semibold text-center shadow-lg hover:shadow-xl transition">
                        Get Started
                    </a>
                </div>
            </div>
        </div>
    </div>
</nav>

