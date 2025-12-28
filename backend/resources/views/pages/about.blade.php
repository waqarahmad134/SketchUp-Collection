@extends('layouts.app')

@section('content')
    @php
        $values = [
            ['icon' => 'target', 'title' => 'Our Mission', 'description' => 'To empower designers worldwide with premium 3D assets that accelerate their creative process.'],
            ['icon' => 'users', 'title' => 'Our Community', 'description' => 'Serving 50,000+ designers globally with high-quality SketchUp models and textures.'],
            ['icon' => 'award', 'title' => 'Quality First', 'description' => 'Every asset is carefully curated and optimized for professional use.'],
            ['icon' => 'heart', 'title' => 'Designer-Focused', 'description' => 'Built by designers, for designers. We understand your workflow needs.'],
        ];
    @endphp

    <section class="py-24 mesh-gradient relative overflow-hidden">
        <div class="absolute top-0 left-1/2 -translate-x-1/2 w-[800px] h-[400px] bg-gradient-to-b from-cyan-500/10 to-transparent rounded-full blur-3xl"></div>

        <div class="container mx-auto px-4 relative z-10">
            <div class="text-center mb-16 space-y-4 max-w-3xl mx-auto">
                <h1 class="text-4xl md:text-6xl font-bold font-display">
                    About <span class="gradient-text">3DAssetHub</span>
                </h1>
                <p class="text-xl text-muted-foreground">
                    We're on a mission to revolutionize how designers access and use 3D assets.
                </p>
            </div>

            <div class="glass-card rounded-3xl p-8 md:p-12 mb-16 max-w-4xl mx-auto">
                <h2 class="text-3xl font-bold font-display mb-6">Our Story</h2>
                <div class="space-y-4 text-muted-foreground">
                    <p>
                        3DAssetHub was born from a simple frustration: finding high-quality 3D assets for SketchUp was time-consuming and expensive. As designers ourselves, we knew there had to be a better way.
                    </p>
                    <p>
                        Today, we've built a curated collection of premium SketchUp models, textures, and bundles serving over 50,000 designers worldwide.
                    </p>
                    <p>
                        We believe every designer deserves access to professional-grade assets without breaking the bank. That's why we offer lifetime access, commercial licenses, and continuously expand our collection.
                    </p>
                </div>
            </div>

            <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-6">
                @foreach($values as $value)
                    <div class="glass-card rounded-3xl p-6 hover-lift text-center">
                        <div class="w-16 h-16 mx-auto mb-4 rounded-2xl bg-gradient-to-br from-cyan-500/20 to-violet-500/20 flex items-center justify-center">
                            <i data-lucide="{{ $value['icon'] }}" class="w-8 h-8 text-cyan-400"></i>
                        </div>
                        <h3 class="text-xl font-bold font-display mb-3">{{ $value['title'] }}</h3>
                        <p class="text-sm text-muted-foreground">{{ $value['description'] }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>
@endsection

