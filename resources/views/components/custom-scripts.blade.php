@php
    use App\Models\CustomScript;
    use App\Models\Setting;
    
    $position = $position ?? 'head';
    $scripts = match($position) {
        'body_start' => CustomScript::getBodyStartScripts(),
        'body_end' => CustomScript::getBodyEndScripts(),
        default => CustomScript::getHeadScripts(),
    };
@endphp

{{-- Google Analytics --}}
@if($position === 'head' && $googleAnalyticsId = Setting::get('google_analytics_id'))
<!-- Google Analytics -->
<script async src="https://www.googletagmanager.com/gtag/js?id={{ $googleAnalyticsId }}"></script>
<script>
    window.dataLayer = window.dataLayer || [];
    function gtag(){dataLayer.push(arguments);}
    gtag('js', new Date());
    gtag('config', '{{ $googleAnalyticsId }}');
</script>
@endif

{{-- Google Tag Manager --}}
@if($position === 'head' && $gtmId = Setting::get('google_tag_manager_id'))
<!-- Google Tag Manager -->
<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
})(window,document,'script','dataLayer','{{ $gtmId }}');</script>
@endif

{{-- Google Tag Manager (noscript) --}}
@if($position === 'body_start' && $gtmId = Setting::get('google_tag_manager_id'))
<!-- Google Tag Manager (noscript) -->
<noscript><iframe src="https://www.googletagmanager.com/ns.html?id={{ $gtmId }}"
height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
@endif

{{-- Facebook Pixel --}}
@if($position === 'head' && $fbPixelId = Setting::get('facebook_pixel_id'))
<!-- Facebook Pixel Code -->
<script>
!function(f,b,e,v,n,t,s)
{if(f.fbq)return;n=f.fbq=function(){n.callMethod?
n.callMethod.apply(n,arguments):n.queue.push(arguments)};
if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
n.queue=[];t=b.createElement(e);t.async=!0;
t.src=v;s=b.getElementsByTagName(e)[0];
s.parentNode.insertBefore(t,s)}(window, document,'script',
'https://connect.facebook.net/en_US/fbevents.js');
fbq('init', '{{ $fbPixelId }}');
fbq('track', 'PageView');
</script>
<noscript><img height="1" width="1" style="display:none"
src="https://www.facebook.com/tr?id={{ $fbPixelId }}&ev=PageView&noscript=1"
/></noscript>
@endif

{{-- Hotjar --}}
@if($position === 'head' && $hotjarId = Setting::get('hotjar_id'))
<!-- Hotjar Tracking Code -->
<script>
    (function(h,o,t,j,a,r){
        h.hj=h.hj||function(){(h.hj.q=h.hj.q||[]).push(arguments)};
        h._hjSettings={hjid:{{ $hotjarId }},hjsv:6};
        a=o.getElementsByTagName('head')[0];
        r=o.createElement('script');r.async=1;
        r.src=t+h._hjSettings.hjid+j+h._hjSettings.hjsv;
        a.appendChild(r);
    })(window,document,'https://static.hotjar.com/c/hotjar-','.js?sv=');
</script>
@endif

{{-- Google Site Verification --}}
@if($position === 'head' && $googleVerification = Setting::get('google_site_verification'))
<meta name="google-site-verification" content="{{ $googleVerification }}">
@endif

{{-- Custom Scripts --}}
@foreach($scripts as $script)
{!! $script->code !!}
@endforeach
