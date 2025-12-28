@extends('layouts.app')

@section('content')
    @php
        $updates = [
            ['date' => 'December 2024', 'title' => 'New Interior Bundle Released', 'description' => 'Added 200+ new interior furniture models and accessories to our collection.', 'type' => 'New Assets'],
            ['date' => 'November 2024', 'title' => 'Landscape Collection Update', 'description' => 'Expanded our landscape bundle with 150+ new trees, plants, and outdoor elements.', 'type' => 'Update'],
            ['date' => 'October 2024', 'title' => 'Texture Library Expansion', 'description' => 'Added 500+ new high-resolution textures across multiple categories.', 'type' => 'New Assets'],
            ['date' => 'September 2024', 'title' => 'Website Redesign', 'description' => 'Completely redesigned our website for better user experience and faster downloads.', 'type' => 'Feature'],
        ];
    @endphp

    <section class="py-24 mesh-gradient relative overflow-hidden">
        <div class="absolute top-0 left-1/2 -translate-x-1/2 w-[800px] h-[400px] bg-gradient-to-b from-cyan-500/10 to-transparent rounded-full blur-3xl"></div>
        
        <div class="container mx-auto px-4 relative z-10">
            <div class="text-center mb-16 space-y-4">
                <div class="inline-flex items-center gap-2 glass-card px-4 py-2 rounded-full">
                    <i data-lucide="sparkles" class="w-4 h-4 text-cyan-400"></i>
                    <span class="text-sm text-cyan-400 font-medium">Latest News</span>
                </div>
                <h1 class="text-4xl md:text-6xl font-bold font-display">
                    Updates & <span class="gradient-text">News</span>
                </h1>
                <p class="text-xl text-muted-foreground max-w-2xl mx-auto">
                    Stay informed about new asset releases, features, and platform updates.
                </p>
            </div>

            <div class="max-w-4xl mx-auto space-y-6">
                @foreach($updates as $update)
                    <div class="glass-card rounded-3xl p-8 hover-lift">
                        <div class="flex items-start justify-between mb-4">
                            <div class="flex items-center gap-3">
                                <i data-lucide="calendar" class="w-5 h-5 text-cyan-400"></i>
                                <span class="text-sm text-muted-foreground">{{ $update['date'] }}</span>
                            </div>
                            <span class="bg-cyan-500/20 text-cyan-400 text-xs font-bold px-3 py-1 rounded-full">
                                {{ $update['type'] }}
                            </span>
                        </div>
                        <h3 class="text-2xl font-bold font-display mb-3">{{ $update['title'] }}</h3>
                        <p class="text-muted-foreground">{{ $update['description'] }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>
@endsection

