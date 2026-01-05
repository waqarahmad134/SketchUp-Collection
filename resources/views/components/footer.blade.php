<footer class="bg-muted/40 border-t border-border py-8">
    <div class="container mx-auto px-4 md:px-8">
        <div class="flex flex-col items-center gap-6">
            <button
                onclick="window.scrollTo({ top: 0, behavior: 'smooth' })"
                class="inline-flex items-center justify-center gap-2 whitespace-nowrap rounded-md text-sm font-medium min-h-9 px-4 py-2 border border-transparent"
                data-testid="button-scroll-top"
            >
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"></path>
                </svg>
                Desplazamiento hacia arriba
            </button>
            
            <nav class="flex flex-wrap justify-center gap-4 md:gap-6">
                <a href="{{ route('about') }}" class="text-sm text-muted-foreground hover:text-foreground transition-colors">
                    Sobre Nosotros
                </a>
                <a href="{{ route('contact') }}" class="text-sm text-muted-foreground hover:text-foreground transition-colors">
                    Contacto
                </a>
                <a href="{{ route('privacy-policy') }}" class="text-sm text-muted-foreground hover:text-foreground transition-colors">
                    Política de Privacidad
                </a>
                <a href="{{ route('terms-conditions') }}" class="text-sm text-muted-foreground hover:text-foreground transition-colors">
                    Términos y Condiciones
                </a>
                <a href="{{ route('cookie-policy') }}" class="text-sm text-muted-foreground hover:text-foreground transition-colors">
                    Política de Cookies
                </a>
            </nav>
            
            <p class="text-sm text-muted-foreground text-center">
                © {{ date('Y') }} Rutificador Chile. Todos los derechos reservados.
            </p>
        </div>
    </div>
</footer>

