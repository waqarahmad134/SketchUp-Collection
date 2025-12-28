@extends('layouts.app')

@section('content')
    <section class="py-24 bg-background relative overflow-hidden">
        <div class="absolute top-0 left-1/2 -translate-x-1/2 w-[800px] h-[400px] bg-gradient-to-b from-violet-500/5 to-transparent rounded-full blur-3xl"></div>

        <div class="container mx-auto px-4 relative z-10">
            <div class="text-center mb-16 space-y-4">
                <span class="inline-block glass-card px-4 py-2 rounded-full text-sm text-violet-400 font-medium">
                    Latest Articles
                </span>
                <h1 class="text-4xl md:text-5xl font-bold font-display">
                    Our <span class="gradient-text">Blog</span>
                </h1>
                <p class="text-xl text-muted-foreground max-w-2xl mx-auto">
                    Tips, tutorials, and insights about 3D design, SketchUp, and interior design trends.
                </p>
            </div>

            @if($posts->isEmpty())
                <div class="text-center py-16">
                    <i data-lucide="file-text" class="w-16 h-16 mx-auto mb-4 text-muted-foreground"></i>
                    <h2 class="text-2xl font-bold font-display mb-2">No Posts Yet</h2>
                    <p class="text-muted-foreground">Check back soon for new blog posts!</p>
                </div>
            @else
                <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
                    @foreach($posts as $post)
                        <a href="{{ url('/blog/' . $post->slug) }}" class="glass-card rounded-3xl overflow-hidden hover-lift group">
                            <div class="relative overflow-hidden">
                                @if($post->featured_image_url)
                                    <img src="{{ $post->featured_image_url }}" alt="{{ $post->title }}" class="w-full h-64 object-cover transition-transform duration-700 group-hover:scale-110">
                                @else
                                    <div class="w-full h-64 bg-gradient-to-br from-cyan-500/20 to-violet-500/20 flex items-center justify-center">
                                        <i data-lucide="file-text" class="w-16 h-16 text-cyan-400/50"></i>
                                    </div>
                                @endif
                                <div class="absolute inset-0 bg-gradient-to-t from-background/90 via-background/20 to-transparent"></div>
                                <div class="absolute bottom-4 left-4 right-4">
                                    <h3 class="text-xl font-bold font-display mb-2 line-clamp-2">{{ $post->title }}</h3>
                                    @if($post->excerpt)
                                        <p class="text-sm text-muted-foreground line-clamp-2">{{ $post->excerpt }}</p>
                                    @endif
                                </div>
                            </div>
                            <div class="p-6 space-y-4">
                                <div class="flex items-center gap-4 text-sm text-muted-foreground">
                                    @if($post->user)
                                        <span class="flex items-center gap-1">
                                            <i data-lucide="user" class="w-4 h-4"></i> {{ $post->user->name }}
                                        </span>
                                    @endif
                                    @if($post->published_at)
                                        <span class="flex items-center gap-1">
                                            <i data-lucide="calendar" class="w-4 h-4"></i> {{ $post->published_at->format('M d, Y') }}
                                        </span>
                                    @endif
                                </div>
                                <div class="w-full inline-flex items-center justify-center gap-2 px-4 py-2 rounded-xl bg-gradient-to-r from-cyan-500 to-violet-500 text-background font-semibold shadow-lg hover:shadow-xl transition group/btn">
                                    Read More
                                    <i data-lucide="arrow-right" class="w-4 h-4 transition-transform group-hover/btn:translate-x-1"></i>
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>

                @if($posts->hasPages())
                    <div class="mt-12 flex justify-center items-center gap-4">
                        @if($posts->onFirstPage())
                            <span class="px-4 py-2 rounded-xl border border-border text-muted-foreground">Previous</span>
                        @else
                            <a href="{{ $posts->previousPageUrl() }}" class="px-4 py-2 rounded-xl border border-border hover:border-foreground transition">Previous</a>
                        @endif
                        <span class="text-muted-foreground">
                            Page {{ $posts->currentPage() }} of {{ $posts->lastPage() }}
                        </span>
                        @if($posts->hasMorePages())
                            <a href="{{ $posts->nextPageUrl() }}" class="px-4 py-2 rounded-xl border border-border hover:border-foreground transition">Next</a>
                        @else
                            <span class="px-4 py-2 rounded-xl border border-border text-muted-foreground">Next</span>
                        @endif
                    </div>
                @endif
            @endif
        </div>
    </section>
@endsection

