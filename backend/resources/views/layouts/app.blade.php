<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? config('app.name', '3DAssetHub') }}</title>
    <meta name="description" content="{{ $metaDescription ?? 'Premium 3D assets, bundles, and blog for designers.' }}">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="antialiased bg-background text-foreground">
    @include('partials.navbar')

    <main class="pt-20">
        @yield('content')
    </main>

    @include('partials.footer')

    <script src="https://unpkg.com/lucide@latest"></script>
    <script>
        window.addEventListener('DOMContentLoaded', () => {
            if (window.lucide?.createIcons) {
                window.lucide.createIcons();
            }
        });
    </script>
    @stack('scripts')
</body>
</html>

