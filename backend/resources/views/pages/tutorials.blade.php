@extends('layouts.app')

@section('content')
    @php
        $tutorials = [
            [
                'id' => 1,
                'title' => 'Getting Started with SketchUp Models',
                'description' => 'Learn how to import and use our SKP files in your SketchUp projects.',
                'duration' => '15 min',
                'level' => 'Beginner',
                'thumbnail' => asset('assets/interior-preview.jpg'),
            ],
            [
                'id' => 2,
                'title' => 'Working with Textures and Materials',
                'description' => 'Master texture application and material customization techniques.',
                'duration' => '20 min',
                'level' => 'Intermediate',
                'thumbnail' => asset('assets/exterior-preview.jpg'),
            ],
            [
                'id' => 3,
                'title' => 'Optimizing 3D Models for Rendering',
                'description' => 'Learn how to optimize your models for faster rendering and better performance.',
                'duration' => '25 min',
                'level' => 'Advanced',
                'thumbnail' => asset('assets/landscape-preview.jpg'),
            ],
        ];
    @endphp

    <section class="py-24 mesh-gradient relative overflow-hidden">
        <div class="absolute top-0 left-1/2 -translate-x-1/2 w-[800px] h-[400px] bg-gradient-to-b from-violet-500/10 to-transparent rounded-full blur-3xl"></div>
        
        <div class="container mx-auto px-4 relative z-10">
            <div class="text-center mb-16 space-y-4">
                <div class="inline-flex items-center gap-2 glass-card px-4 py-2 rounded-full">
                    <i data-lucide="video" class="w-4 h-4 text-cyan-400"></i>
                    <span class="text-sm text-cyan-400 font-medium">Learn & Grow</span>
                </div>
                <h1 class="text-4xl md:text-6xl font-bold font-display">
                    Video <span class="gradient-text">Tutorials</span>
                </h1>
                <p class="text-xl text-muted-foreground max-w-2xl mx-auto">
                    Step-by-step video guides to help you master 3D design with our assets.
                </p>
            </div>

            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8 max-w-6xl mx-auto">
                @foreach($tutorials as $tutorial)
                    <a href="#" class="glass-card rounded-3xl overflow-hidden hover-lift group">
                        <div class="relative h-48 overflow-hidden">
                            <img src="{{ $tutorial['thumbnail'] }}" alt="{{ $tutorial['title'] }}" class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110">
                            <div class="absolute inset-0 bg-gradient-to-t from-background/90 to-transparent"></div>
                            <div class="absolute inset-0 flex items-center justify-center">
                                <div class="w-16 h-16 rounded-full bg-cyan-500/80 backdrop-blur-sm flex items-center justify-center group-hover:scale-110 transition-transform">
                                    <i data-lucide="play-circle" class="w-8 h-8 text-background"></i>
                                </div>
                            </div>
                            <div class="absolute bottom-4 left-4 right-4 flex items-center justify-between">
                                <span class="bg-cyan-500/20 text-cyan-400 text-xs font-bold px-3 py-1 rounded-full">
                                    {{ $tutorial['level'] }}
                                </span>
                                <div class="flex items-center gap-1 text-xs text-foreground bg-background/80 backdrop-blur-sm px-2 py-1 rounded-full">
                                    <i data-lucide="clock" class="w-3 h-3"></i>
                                    <span>{{ $tutorial['duration'] }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="p-6">
                            <h3 class="text-xl font-bold font-display mb-2 group-hover:text-cyan-400 transition-colors">
                                {{ $tutorial['title'] }}
                            </h3>
                            <p class="text-sm text-muted-foreground mb-4 line-clamp-2">
                                {{ $tutorial['description'] }}
                            </p>
                            <div class="flex items-center gap-2 text-cyan-400 font-medium text-sm">
                                Watch Tutorial
                                <i data-lucide="arrow-right" class="w-4 h-4 transition-transform group-hover:translate-x-1"></i>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>
        </div>
    </section>
@endsection

