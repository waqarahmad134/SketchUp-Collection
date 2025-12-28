@extends('layouts.app')

@section('content')
    @php
        $supportOptions = [
            ['icon' => 'book', 'title' => 'Documentation', 'description' => 'Browse our comprehensive guides and tutorials', 'href' => route('documentation')],
            ['icon' => 'message-square', 'title' => 'FAQ', 'description' => 'Find answers to frequently asked questions', 'href' => '#'],
            ['icon' => 'mail', 'title' => 'Email Support', 'description' => 'Get help via email - we respond within 24 hours', 'href' => route('contact')],
            ['icon' => 'help-circle', 'title' => 'Live Chat', 'description' => 'Chat with our support team in real-time', 'href' => '#'],
        ];

        $faqs = [
            ['q' => 'How do I download assets?', 'a' => "Once you purchase a bundle or individual asset, you'll receive a download link via email. You can also access all your purchases from your account dashboard."],
            ['q' => 'What file formats are included?', 'a' => 'Our bundles include SKP files (SketchUp), SKM texture files, and sometimes additional formats like OBJ or FBX depending on the bundle.'],
            ['q' => 'Can I use assets commercially?', 'a' => 'Yes! All our assets come with a commercial license, allowing you to use them in client projects and commercial work without restrictions.'],
            ['q' => 'Do I get lifetime access?', 'a' => 'Yes, all purchases include lifetime access. You can download your assets anytime, even if we update or add new content to the bundle.'],
        ];
    @endphp

    <section class="py-24 mesh-gradient relative overflow-hidden">
        <div class="absolute top-0 left-1/2 -translate-x-1/2 w-[800px] h-[400px] bg-gradient-to-b from-violet-500/10 to-transparent rounded-full blur-3xl"></div>
        
        <div class="container mx-auto px-4 relative z-10">
            <div class="text-center mb-16 space-y-4">
                <div class="inline-flex items-center gap-2 glass-card px-4 py-2 rounded-full">
                    <i data-lucide="help-circle" class="w-4 h-4 text-cyan-400"></i>
                    <span class="text-sm text-cyan-400 font-medium">We're Here to Help</span>
                </div>
                <h1 class="text-4xl md:text-6xl font-bold font-display">
                    Support <span class="gradient-text">Center</span>
                </h1>
                <p class="text-xl text-muted-foreground max-w-2xl mx-auto">
                    Find answers, get help, and make the most of your SketchUp Collection experience.
                </p>
            </div>

            <div class="max-w-4xl mx-auto mb-12">
                <div class="glass-card rounded-3xl p-6 flex items-center gap-4">
                    <i data-lucide="search" class="w-6 h-6 text-muted-foreground"></i>
                    <input
                        type="search"
                        placeholder="Search for help..."
                        class="flex-1 bg-transparent border-none outline-none text-foreground placeholder:text-muted-foreground"
                    >
                </div>
            </div>

            <div class="grid md:grid-cols-2 gap-6 mb-16 max-w-4xl mx-auto">
                @foreach($supportOptions as $option)
                    <a href="{{ $option['href'] }}" class="glass-card rounded-3xl p-8 hover-lift group">
                        <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-cyan-500/20 to-violet-500/20 flex items-center justify-center mb-6 group-hover:scale-110 transition-transform">
                            <i data-lucide="{{ $option['icon'] }}" class="w-8 h-8 text-cyan-400"></i>
                        </div>
                        <h3 class="text-xl font-bold font-display mb-3">{{ $option['title'] }}</h3>
                        <p class="text-muted-foreground">{{ $option['description'] }}</p>
                    </a>
                @endforeach
            </div>

            <div class="max-w-4xl mx-auto">
                <h2 class="text-3xl font-bold font-display mb-8 text-center">
                    Frequently Asked Questions
                </h2>
                <div class="space-y-4">
                    @foreach($faqs as $faq)
                        <div class="glass-card rounded-3xl p-6">
                            <h3 class="text-lg font-bold font-display mb-3">{{ $faq['q'] }}</h3>
                            <p class="text-muted-foreground">{{ $faq['a'] }}</p>
                        </div>
                    @endforeach
                </div>

                <div class="mt-12 glass-card rounded-3xl p-8 text-center">
                    <h3 class="text-2xl font-bold font-display mb-4">
                        Still Need Help?
                    </h3>
                    <p class="text-muted-foreground mb-6">
                        Can't find what you're looking for? Our support team is ready to help.
                    </p>
                    <a href="{{ route('contact') }}" class="inline-flex items-center justify-center gap-2 px-6 py-3 rounded-xl bg-gradient-to-r from-cyan-500 to-violet-500 text-background font-semibold shadow-lg hover:shadow-xl transition">
                        Contact Support
                    </a>
                </div>
            </div>
        </div>
    </section>
@endsection

