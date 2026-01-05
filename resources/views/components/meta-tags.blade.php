@php
$siteUrl = config('app.url', 'https://www.rutificadorchile.app');
$siteName = 'Rutificador Chile';
$defaultImage = 'https://images.unsplash.com/photo-1554224155-8d04cb21cd6c?w=1200&h=630&fit=crop';

// Get values from $meta array if provided, otherwise use defaults
$title = $meta['title'] ?? 'Rutificador Chile Nombre Rut y Firma';
$description = $meta['description'] ?? 'Rutificador Chile: Encuentra Nombre, RUT y Firma con precisión y rapidez.';
$canonical = $meta['canonical'] ?? url()->current();
$ogImage = $meta['og_image'] ?? $meta['image'] ?? $defaultImage;
$ogType = $meta['og_type'] ?? 'website';
$keywords = $meta['keywords'] ?? null;
$structuredData = $meta['structured_data'] ?? null;
@endphp

{{-- Basic Meta Tags --}}
<meta name="description" content="{{ $description }}">
@if($keywords)
<meta name="keywords" content="{{ $keywords }}">
@endif

{{-- Canonical URL --}}
<link rel="canonical" href="{{ $canonical }}">

{{-- Open Graph Meta Tags --}}
<meta property="og:title" content="{{ $title }}">
<meta property="og:description" content="{{ $description }}">
<meta property="og:image" content="{{ $ogImage }}">
<meta property="og:url" content="{{ $canonical }}">
<meta property="og:type" content="{{ $ogType }}">
<meta property="og:site_name" content="{{ $siteName }}">
<meta property="og:locale" content="es_CL">

{{-- Twitter Card Meta Tags --}}
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="{{ $title }}">
<meta name="twitter:description" content="{{ $description }}">
<meta name="twitter:image" content="{{ $ogImage }}">

{{-- JSON-LD Structured Data --}}
@if($structuredData)
<script type="application/ld+json">
{!! json_encode($structuredData, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) !!}
</script>
@endif
