@php
    use App\Models\Setting;
    $favicon = Setting::get('favicon', '/favicon.ico');
    $appleTouchIcon = Setting::get('apple_touch_icon', '/apple-touch-icon.png');
    $favicon32 = Setting::get('favicon_32', '/favicon-32x32.png');
    $favicon16 = Setting::get('favicon_16', '/favicon-16x16.png');
    $manifest = Setting::get('site_manifest', '/site.webmanifest');
@endphp

<link rel="icon" href="{{ asset($favicon) }}">
<link rel="apple-touch-icon" sizes="180x180" href="{{ asset($appleTouchIcon) }}">
<link rel="icon" type="image/png" sizes="32x32" href="{{ asset($favicon32) }}">
<link rel="icon" type="image/png" sizes="16x16" href="{{ asset($favicon16) }}">
<link rel="manifest" href="{{ asset($manifest) }}">
<meta name="theme-color" content="#ffffff">
