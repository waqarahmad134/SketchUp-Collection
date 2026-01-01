@php
    $features = [
        ['icon' => 'zap', 'title' => 'Instant Download', 'description' => 'Get immediate access to all assets via Google Drive or Dropbox. No waiting time.'],
        ['icon' => 'file-check', 'title' => 'Ready to Use', 'description' => 'All models are optimized, clean, and ready to drop into your SketchUp projects.'],
        ['icon' => 'download', 'title' => 'Lifetime Access', 'description' => 'One-time payment for permanent access. No subscriptions, no hidden fees.'],
        ['icon' => 'shield', 'title' => 'Commercial License', 'description' => 'Use our assets in client projects and commercial work without restrictions.'],
        ['icon' => 'globe', 'title' => 'Global Community', 'description' => 'Join 50,000+ designers worldwide who trust our premium asset library.'],
        ['icon' => 'headphones', 'title' => '24/7 Support', 'description' => "Get help anytime via WhatsApp or email. We're always here for you."],
    ];
@endphp

<section class="py-24 mesh-gradient relative overflow-hidden">
    <div class="absolute top-1/2 left-0 w-96 h-96 bg-violet-500/10 rounded-full blur-3xl -translate-y-1/2"></div>
    <div class="absolute top-1/2 right-0 w-96 h-96 bg-cyan-500/10 rounded-full blur-3xl -translate-y-1/2"></div>

    <div class="container mx-auto px-4 relative z-10">
        <div class="text-center mb-16 space-y-4">
            <span class="inline-block glass-card px-4 py-2 rounded-full text-sm text-violet-400 font-medium">
                Why Choose Us
            </span>
            <h2 class="text-4xl md:text-5xl font-bold font-display">
                Everything You <span class="gradient-text">Need</span>
            </h2>
            <p class="text-xl text-muted-foreground max-w-2xl mx-auto">
                We've designed our platform with designers in mind. Here's why thousands choose us.
            </p>
        </div>

        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($features as $index => $feature)
                <div class="glass-card p-8 rounded-3xl hover-lift group animate-slide-in-up" style="animation-delay: {{ $index * 0.1 }}s">
                    <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-cyan-500/20 to-violet-500/20 flex items-center justify-center mb-6 group-hover:scale-110 transition-transform duration-300">
                        <i data-lucide="{{ $feature['icon'] }}" class="w-7 h-7 text-cyan-400"></i>
                    </div>
                    <h3 class="text-xl font-bold font-display mb-3">{{ $feature['title'] }}</h3>
                    <p class="text-muted-foreground">{{ $feature['description'] }}</p>
                </div>
            @endforeach
        </div>
    </div>
</section>

