@extends('layouts.app')

@section('content')
    @include('partials.sections.hero')
    @include('partials.sections.stats')
    @include('partials.sections.bundles', ['products' => $products ?? collect()])
    @include('partials.sections.features')
    @include('partials.sections.blog', ['featuredPosts' => $featuredPosts ?? collect()])
    @include('partials.sections.testimonials')
    @include('partials.sections.cta')
@endsection

@push('scripts')
    <script>
        // Auto-check daily login status on home page load only
        window.addEventListener('DOMContentLoaded', () => {
            // Check daily login status after page load (with delay) - for everyone
            setTimeout(async () => {
                const data = await checkDailyLoginStatus();
                if (data) {
                    // Only auto-show popup if:
                    // 1. Daily login is enabled
                    // 2. User is not logged in (to encourage signup) OR user can claim (hasn't claimed today)
                    // Don't auto-show if user is logged in and already claimed today
                    const isLoggedIn = {{ auth()->check() ? 'true' : 'false' }};
                    const shouldAutoShow = data.enabled !== false && 
                        (!isLoggedIn || (isLoggedIn && data.can_claim));
                    
                    if (shouldAutoShow) {
                        showDailyLoginModal(data);
                    }
                }
            }, 2000); // Show popup after 2 seconds
        });
    </script>
@endpush
