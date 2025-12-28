@extends('layouts.app')

@section('content')
    @php
        $docsCategories = [
            [
                'title' => 'Getting Started',
                'description' => 'Learn the basics of using 3DAssetHub',
                'articles' => [
                    'Introduction to 3DAssetHub',
                    'Creating Your Account',
                    'Downloading Your First Asset',
                    'Understanding File Formats',
                ],
            ],
            [
                'title' => 'Asset Usage',
                'description' => 'How to use assets in your projects',
                'articles' => [
                    'Importing SKP Files to SketchUp',
                    'Working with Textures',
                    'Optimizing Models',
                    'Best Practices',
                ],
            ],
            [
                'title' => 'Licensing',
                'description' => 'Understanding our licensing terms',
                'articles' => [
                    'Commercial License Explained',
                    'Usage Rights',
                    'Attribution Requirements',
                    'License FAQ',
                ],
            ],
        ];
    @endphp

    <section class="py-24 mesh-gradient relative overflow-hidden">
        <div class="absolute top-0 left-1/2 -translate-x-1/2 w-[800px] h-[400px] bg-gradient-to-b from-cyan-500/10 to-transparent rounded-full blur-3xl"></div>
        
        <div class="container mx-auto px-4 relative z-10">
            <div class="text-center mb-16 space-y-4">
                <div class="inline-flex items-center gap-2 glass-card px-4 py-2 rounded-full">
                    <i data-lucide="book" class="w-4 h-4 text-cyan-400"></i>
                    <span class="text-sm text-cyan-400 font-medium">User Guides</span>
                </div>
                <h1 class="text-4xl md:text-6xl font-bold font-display">
                    Documentation
                </h1>
                <p class="text-xl text-muted-foreground max-w-2xl mx-auto">
                    Everything you need to know about using 3DAssetHub assets effectively.
                </p>
            </div>

            <div class="max-w-4xl mx-auto mb-8">
                <div class="glass-card rounded-3xl p-6 flex items-center gap-4">
                    <i data-lucide="search" class="w-6 h-6 text-muted-foreground"></i>
                    <input
                        type="search"
                        placeholder="Search documentation..."
                        class="flex-1 bg-transparent border-none outline-none text-foreground placeholder:text-muted-foreground"
                    >
                </div>
            </div>

            <div class="grid md:grid-cols-3 gap-8 max-w-6xl mx-auto">
                @foreach($docsCategories as $category)
                    <div class="glass-card rounded-3xl p-8 hover-lift">
                        <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-cyan-500/20 to-violet-500/20 flex items-center justify-center mb-4">
                            <i data-lucide="file-text" class="w-6 h-6 text-cyan-400"></i>
                        </div>
                        <h3 class="text-xl font-bold font-display mb-2">{{ $category['title'] }}</h3>
                        <p class="text-sm text-muted-foreground mb-6">{{ $category['description'] }}</p>
                        <ul class="space-y-3">
                            @foreach($category['articles'] as $article)
                                <li>
                                    <a href="#" class="flex items-center gap-2 text-sm text-muted-foreground hover:text-cyan-400 transition-colors group">
                                        <i data-lucide="arrow-right" class="w-4 h-4 opacity-0 group-hover:opacity-100 transition-opacity"></i>
                                        <span>{{ $article }}</span>
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endforeach
            </div>
        </div>
    </section>
@endsection

