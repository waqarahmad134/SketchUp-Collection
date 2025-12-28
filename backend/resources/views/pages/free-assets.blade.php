@extends('layouts.app')

@section('content')
    @php
        $freeAssets = [
            ['category' => 'Furniture', 'count' => '50+', 'description' => 'Free furniture models for your projects'],
            ['category' => 'Textures', 'count' => '100+', 'description' => 'High-quality texture files'],
            ['category' => 'Architecture', 'count' => '30+', 'description' => 'Basic architectural elements'],
            ['category' => 'Landscape', 'count' => '25+', 'description' => 'Trees, plants, and outdoor elements'],
        ];
    @endphp

    <section class="py-24 mesh-gradient relative overflow-hidden">
        <div class="absolute top-0 left-1/2 -translate-x-1/2 w-[800px] h-[400px] bg-gradient-to-b from-violet-500/10 to-transparent rounded-full blur-3xl"></div>
        
        <div class="container mx-auto px-4 relative z-10">
            <div class="text-center mb-16 space-y-4">
                <div class="inline-flex items-center gap-2 glass-card px-4 py-2 rounded-full">
                    <i data-lucide="gift" class="w-4 h-4 text-cyan-400"></i>
                    <span class="text-sm text-cyan-400 font-medium">Free Resources</span>
                </div>
                <h1 class="text-4xl md:text-6xl font-bold font-display">
                    Free <span class="gradient-text">Assets</span>
                </h1>
                <p class="text-xl text-muted-foreground max-w-2xl mx-auto">
                    Download premium-quality 3D assets completely free. No credit card required.
                </p>
            </div>

            <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-6 mb-12">
                @foreach($freeAssets as $asset)
                    <div class="glass-card rounded-3xl p-6 hover-lift text-center">
                        <div class="w-16 h-16 mx-auto mb-4 rounded-2xl bg-gradient-to-br from-cyan-500/20 to-violet-500/20 flex items-center justify-center">
                            <i data-lucide="file-box" class="w-8 h-8 text-cyan-400"></i>
                        </div>
                        <h3 class="text-xl font-bold font-display mb-2">{{ $asset['category'] }}</h3>
                        <p class="text-3xl font-bold gradient-text mb-2">{{ $asset['count'] }}</p>
                        <p class="text-sm text-muted-foreground">{{ $asset['description'] }}</p>
                    </div>
                @endforeach
            </div>

            <div class="text-center space-y-6">
                <div class="glass-card rounded-3xl p-8 max-w-2xl mx-auto">
                    <h2 class="text-2xl font-bold font-display mb-4">
                        Get Started with Free Assets
                    </h2>
                    <p class="text-muted-foreground mb-6">
                        Sign up for free and instantly access our collection of premium free assets. No credit card required, no hidden fees.
                    </p>
                    <div class="flex flex-wrap justify-center gap-4">
                        <button class="px-6 py-3 rounded-xl bg-gradient-to-r from-cyan-500 to-violet-500 text-background font-semibold shadow-lg hover:shadow-xl transition inline-flex items-center gap-2">
                            <i data-lucide="download" class="w-5 h-5"></i>
                            Download Free Assets
                        </button>
                        <button class="px-6 py-3 rounded-xl border border-border text-foreground font-semibold hover:border-foreground transition">
                            View All Free Assets
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

