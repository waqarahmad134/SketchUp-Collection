<header class="sticky top-0 z-50 bg-primary text-primary-foreground shadow-md" x-data="{ mobileMenuOpen: false, mounted: false }" x-init="mounted = true">
    <div class="container mx-auto px-4 md:px-8">
        <div class="flex items-center justify-between h-16">
            <a href="{{ route('home') }}" class="flex items-center gap-2 hover-elevate active-elevate-2 px-3 py-2 rounded-md" data-testid="link-home">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 bg-white rounded-full flex items-center justify-center">
                        <span class="text-primary font-bold text-sm">R</span>
                    </div>
                    <span class="font-bold text-lg hidden sm:inline">RUTIFICADOR CONSULTA RUT</span>
                    <span class="font-bold text-lg sm:hidden">RUT</span>
                </div>
            </a>

            <nav class="hidden lg:flex items-center gap-1">
                <a href="{{ route('rut.search-person') }}" class="px-3 py-2 rounded-md text-sm font-medium hover-elevate active-elevate-2 transition-colors {{ request()->routeIs('rut.search-person') ? 'bg-white/20' : '' }}" data-testid="link-buscar-persona">
                    Buscar Persona por RUT
                </a>
                <a href="{{ route('rut.business') }}" class="px-3 py-2 rounded-md text-sm font-medium hover-elevate active-elevate-2 transition-colors {{ request()->routeIs('rut.business') ? 'bg-white/20' : '' }}" data-testid="link-rutificador-empresas">
                    Rutificador Empresas
                </a>
                <a href="{{ route('rut.generator') }}" class="px-3 py-2 rounded-md text-sm font-medium hover-elevate active-elevate-2 transition-colors {{ request()->routeIs('rut.generator') ? 'bg-white/20' : '' }}" data-testid="link-generador-rut">
                    Generador de RUT
                </a>
                <a href="{{ route('rut.verifier') }}" class="px-3 py-2 rounded-md text-sm font-medium hover-elevate active-elevate-2 transition-colors {{ request()->routeIs('rut.verifier') ? 'bg-white/20' : '' }}" data-testid="link-verificador-rut">
                    Rut Verificador
                </a>
                <a href="{{ route('blog.index') }}" class="px-3 py-2 rounded-md text-sm font-medium hover-elevate active-elevate-2 transition-colors {{ request()->routeIs('blog.*') ? 'bg-white/20' : '' }}" data-testid="link-blog">
                    Blog
                </a>
            </nav>

            <div class="flex items-center gap-2">
                <button
                    @click="document.documentElement.classList.toggle('dark'); localStorage.setItem('theme', document.documentElement.classList.contains('dark') ? 'dark' : 'light')"
                    class="inline-flex items-center justify-center gap-2 whitespace-nowrap rounded-md text-sm font-medium min-h-9 w-9 border border-transparent text-primary-foreground hover:bg-white/20"
                    data-testid="button-theme-toggle"
                    aria-label="Toggle theme"
                >
                    <template x-if="mounted && document.documentElement.classList.contains('dark')">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"></path>
                        </svg>
                    </template>
                    <template x-if="mounted && !document.documentElement.classList.contains('dark')">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path>
                        </svg>
                    </template>
                </button>

                <button
                    @click="mobileMenuOpen = !mobileMenuOpen"
                    class="lg:hidden inline-flex items-center justify-center gap-2 whitespace-nowrap rounded-md text-sm font-medium min-h-9 w-9 border border-transparent text-primary-foreground hover:bg-white/20"
                    data-testid="button-mobile-menu"
                >
                    <template x-if="!mobileMenuOpen">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                        </svg>
                    </template>
                    <template x-if="mobileMenuOpen">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </template>
                </button>
            </div>
        </div>

        <nav x-show="mobileMenuOpen" class="lg:hidden pb-4 space-y-1" x-cloak>
            <a href="{{ route('rut.search-person') }}" class="block px-3 py-2 rounded-md text-sm font-medium hover-elevate active-elevate-2 {{ request()->routeIs('rut.search-person') ? 'bg-white/20' : '' }}" @click="mobileMenuOpen = false" data-testid="link-mobile-buscar-persona">
                Buscar Persona por RUT
            </a>
            <a href="{{ route('rut.business') }}" class="block px-3 py-2 rounded-md text-sm font-medium hover-elevate active-elevate-2 {{ request()->routeIs('rut.business') ? 'bg-white/20' : '' }}" @click="mobileMenuOpen = false" data-testid="link-mobile-rutificador-empresas">
                Rutificador Empresas
            </a>
            <a href="{{ route('rut.generator') }}" class="block px-3 py-2 rounded-md text-sm font-medium hover-elevate active-elevate-2 {{ request()->routeIs('rut.generator') ? 'bg-white/20' : '' }}" @click="mobileMenuOpen = false" data-testid="link-mobile-generador-rut">
                Generador de RUT
            </a>
            <a href="{{ route('rut.verifier') }}" class="block px-3 py-2 rounded-md text-sm font-medium hover-elevate active-elevate-2 {{ request()->routeIs('rut.verifier') ? 'bg-white/20' : '' }}" @click="mobileMenuOpen = false" data-testid="link-mobile-verificador-rut">
                Rut Verificador
            </a>
            <a href="{{ route('blog.index') }}" class="block px-3 py-2 rounded-md text-sm font-medium hover-elevate active-elevate-2 {{ request()->routeIs('blog.*') ? 'bg-white/20' : '' }}" @click="mobileMenuOpen = false" data-testid="link-mobile-blog">
                Blog
            </a>
        </nav>
    </div>
</header>

