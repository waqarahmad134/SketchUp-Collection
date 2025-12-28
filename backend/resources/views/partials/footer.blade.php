@php
    $currentYear = now()->year;
    $footerLinks = [
        'Product' => [
            ['name' => 'Bundles', 'href' => '/bundles'],
            ['name' => 'Free Assets', 'href' => '/free-assets'],
            ['name' => 'Pricing', 'href' => '/pricing'],
            ['name' => 'Updates', 'href' => '/updates'],
        ],
        'Company' => [
            ['name' => 'About', 'href' => '/about'],
            ['name' => 'Blog', 'href' => '/blog'],
            ['name' => 'Careers', 'href' => '/careers'],
            ['name' => 'Contact', 'href' => '/contact'],
        ],
        'Resources' => [
            ['name' => 'Documentation', 'href' => '/documentation'],
            ['name' => 'Tutorials', 'href' => '/tutorials'],
            ['name' => 'Community', 'href' => '/community'],
            ['name' => 'Support', 'href' => '/support'],
        ],
        'Legal' => [
            ['name' => 'Privacy Policy', 'href' => '/privacy-policy'],
            ['name' => 'Terms of Service', 'href' => '/terms-of-service'],
            ['name' => 'License', 'href' => '/license'],
        ],
    ];

    $socialLinks = [
        ['icon' => 'twitter', 'href' => '#'],
        ['icon' => 'instagram', 'href' => '#'],
        ['icon' => 'youtube', 'href' => '#'],
        ['icon' => 'linkedin', 'href' => '#'],
    ];
@endphp

<footer class="bg-card border-t border-border">
    <div class="container mx-auto px-4 py-16">
        <div class="grid grid-cols-2 md:grid-cols-6 gap-8">
            <div class="col-span-2 space-y-4">
                <a href="{{ url('/') }}" class="flex items-center gap-2 group">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-cyan-500 to-violet-500 flex items-center justify-center">
                        <i data-lucide="box" class="w-5 h-5 text-background"></i>
                    </div>
                    <span class="text-xl font-bold font-display">
                        <span class="gradient-text">3D</span>AssetHub
                    </span>
                </a>
                <p class="text-muted-foreground text-sm max-w-xs">
                    Premium 3D assets for designers. Elevate your projects with our curated collection of professional models.
                </p>
                <div class="flex gap-3">
                    @foreach($socialLinks as $social)
                        <a
                            href="{{ $social['href'] }}"
                            class="w-10 h-10 rounded-xl glass-card flex items-center justify-center text-muted-foreground hover:text-cyan-400 hover:border-cyan-500/50 transition-colors"
                            aria-label="{{ $social['icon'] }}"
                        >
                            <i data-lucide="{{ $social['icon'] }}" class="w-5 h-5"></i>
                        </a>
                    @endforeach
                </div>
            </div>

            @foreach($footerLinks as $category => $links)
                <div>
                    <h4 class="font-semibold font-display mb-4">{{ $category }}</h4>
                    <ul class="space-y-2">
                        @foreach($links as $link)
                            <li>
                                <a
                                    href="{{ $link['href'] }}"
                                    class="text-sm text-muted-foreground hover:text-cyan-400 transition-colors"
                                >
                                    {{ $link['name'] }}
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endforeach
        </div>

        <div class="border-t border-border mt-12 pt-8 flex flex-col md:flex-row justify-between items-center gap-4">
            <p class="text-sm text-muted-foreground">
                © {{ $currentYear }} 3DAssetHub. All rights reserved.
            </p>
            <p class="text-sm text-muted-foreground">
                Made with ❤️ for designers worldwide
            </p>
        </div>
    </div>
</footer>

