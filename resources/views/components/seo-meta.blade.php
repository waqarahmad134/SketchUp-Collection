@php
    $seoService = app(\App\Services\SeoService::class);
    $seoData = $seoService->getAllSeoData($model ?? null);
@endphp

{{-- Basic Meta Tags --}}
<title>{{ $seoData['title'] }}</title>
<meta name="description" content="{{ $seoData['description'] }}">
<meta name="robots" content="{{ $seoData['robots'] }}">

{{-- Canonical URL --}}
<link rel="canonical" href="{{ $seoData['canonical_url'] }}">

{{-- Open Graph Tags --}}
<meta property="og:title" content="{{ $seoData['og']['title'] }}">
<meta property="og:description" content="{{ $seoData['og']['description'] }}">
<meta property="og:image" content="{{ $seoData['og']['image'] }}">
<meta property="og:url" content="{{ $seoData['og']['url'] }}">
<meta property="og:type" content="{{ $seoData['og']['type'] }}">
<meta property="og:site_name" content="{{ $seoData['og']['site_name'] }}">

{{-- Twitter Card Tags --}}
<meta name="twitter:card" content="{{ $seoData['twitter']['card'] }}">
@if($seoData['twitter']['site'])
<meta name="twitter:site" content="{{ $seoData['twitter']['site'] }}">
@endif
<meta name="twitter:title" content="{{ $seoData['twitter']['title'] }}">
<meta name="twitter:description" content="{{ $seoData['twitter']['description'] }}">
<meta name="twitter:image" content="{{ $seoData['twitter']['image'] }}">

{{-- Published and Modified Dates --}}
@if(isset($model) && isset($model->published_at))
<meta property="article:published_time" content="{{ $model->published_at->toIso8601String() }}">
@endif
@if(isset($model) && isset($model->updated_at))
<meta property="article:modified_time" content="{{ $model->updated_at->toIso8601String() }}">
@endif

{{-- Schema.org JSON-LD --}}
@if(!empty($seoData['schema']))
<script type="application/ld+json">
{!! json_encode($seoData['schema'], JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) !!}
</script>
@endif
