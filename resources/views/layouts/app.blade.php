<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
    {{-- SEO Meta Tags --}}
    <x-seo-meta :model="$seoModel ?? null" />
    
    {{-- Favicons --}}
    <x-favicons />
    
    {{-- Custom Scripts (Head) --}}
    <x-custom-scripts position="head" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    @stack('head')
</head>
<body
    class="antialiased bg-background text-foreground"
    data-toast-success="{{ session('status') }}"
    data-toast-error="{{ session('error') ?? ($errors->first() ?? '') }}"
>
    {{-- Custom Scripts (Body Start) --}}
    <x-custom-scripts position="body_start" />
    
    @include('partials.navbar')

    <main class="pt-20">
        @yield('content')
    </main>

    @include('partials.footer')

    <div id="toast-root" class="fixed top-5 right-5 z-[9999] space-y-3"></div>

    <script src="https://unpkg.com/lucide@latest"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        window.addEventListener('DOMContentLoaded', () => {
            if (window.lucide?.createIcons) {
                window.lucide.createIcons();
            }
        });
    </script>
    @stack('scripts')
    
    {{-- Custom Scripts (Body End) --}}
    <x-custom-scripts position="body_end" />
</body>
</html>

