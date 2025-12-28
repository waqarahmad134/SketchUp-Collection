@extends('layouts.app')

@section('content')
    @php
        $communityStats = [
            ['icon' => 'users', 'value' => '50K+', 'label' => 'Active Members'],
            ['icon' => 'message-square', 'value' => '10K+', 'label' => 'Discussions'],
            ['icon' => 'award', 'value' => '5K+', 'label' => 'Projects Shared'],
        ];

        $socialLinks = [
            ['icon' => 'twitter', 'name' => 'Twitter', 'href' => '#', 'color' => 'text-blue-400'],
            ['icon' => 'instagram', 'name' => 'Instagram', 'href' => '#', 'color' => 'text-pink-400'],
            ['icon' => 'youtube', 'name' => 'YouTube', 'href' => '#', 'color' => 'text-red-400'],
            ['icon' => 'linkedin', 'name' => 'LinkedIn', 'href' => '#', 'color' => 'text-blue-500'],
        ];
    @endphp

    <section class="py-24 mesh-gradient relative overflow-hidden">
        <div class="absolute top-0 left-1/2 -translate-x-1/2 w-[800px] h-[400px] bg-gradient-to-b from-cyan-500/10 to-transparent rounded-full blur-3xl"></div>
        
        <div class="container mx-auto px-4 relative z-10">
            <div class="text-center mb-16 space-y-4">
                <div class="inline-flex items-center gap-2 glass-card px-4 py-2 rounded-full">
                    <i data-lucide="users" class="w-4 h-4 text-cyan-400"></i>
                    <span class="text-sm text-cyan-400 font-medium">Join Us</span>
                </div>
                <h1 class="text-4xl md:text-6xl font-bold font-display">
                    Our <span class="gradient-text">Community</span>
                </h1>
                <p class="text-xl text-muted-foreground max-w-2xl mx-auto">
                    Connect with designers worldwide, share your work, and grow together.
                </p>
            </div>

            <div class="grid md:grid-cols-3 gap-6 mb-16 max-w-4xl mx-auto">
                @foreach($communityStats as $stat)
                    <div class="glass-card rounded-3xl p-6 text-center">
                        <div class="w-16 h-16 mx-auto mb-4 rounded-2xl bg-gradient-to-br from-cyan-500/20 to-violet-500/20 flex items-center justify-center">
                            <i data-lucide="{{ $stat['icon'] }}" class="w-8 h-8 text-cyan-400"></i>
                        </div>
                        <p class="text-4xl font-bold font-display gradient-text mb-2">
                            {{ $stat['value'] }}
                        </p>
                        <p class="text-sm text-muted-foreground">{{ $stat['label'] }}</p>
                    </div>
                @endforeach
            </div>

            <div class="max-w-4xl mx-auto space-y-8">
                <div class="glass-card rounded-3xl p-8 text-center">
                    <h2 class="text-3xl font-bold font-display mb-4">
                        Join the Conversation
                    </h2>
                    <p class="text-muted-foreground mb-6 max-w-2xl mx-auto">
                        Connect with fellow designers, share your projects, ask questions, and get feedback from the community.
                    </p>
                    <div class="flex flex-wrap justify-center gap-4">
                        <button class="px-6 py-3 rounded-xl bg-gradient-to-r from-cyan-500 to-violet-500 text-background font-semibold shadow-lg hover:shadow-xl transition">
                            Join Discord
                        </button>
                        <button class="px-6 py-3 rounded-xl border border-border text-foreground font-semibold hover:border-foreground transition">
                            Visit Forum
                        </button>
                    </div>
                </div>

                <div class="glass-card rounded-3xl p-8">
                    <h2 class="text-2xl font-bold font-display mb-6">Follow Us</h2>
                    <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-4">
                        @foreach($socialLinks as $social)
                            <a href="{{ $social['href'] }}" class="glass-card rounded-2xl p-6 hover-lift text-center group">
                                <i data-lucide="{{ $social['icon'] }}" class="w-8 h-8 mx-auto mb-3 {{ $social['color'] }} group-hover:scale-110 transition-transform"></i>
                                <p class="font-semibold">{{ $social['name'] }}</p>
                            </a>
                        @endforeach
                    </div>
                </div>

                <div class="glass-card rounded-3xl p-8">
                    <div class="flex items-center gap-4 mb-6">
                        <i data-lucide="share-2" class="w-6 h-6 text-cyan-400"></i>
                        <h2 class="text-2xl font-bold font-display">Share Your Work</h2>
                    </div>
                    <p class="text-muted-foreground mb-6">
                        Tag us on social media with #SketchUpCollection to get featured! We love seeing what you create with our assets.
                    </p>
                    <button class="px-6 py-3 rounded-xl border border-border text-foreground font-semibold hover:border-foreground transition">
                        View Gallery
                    </button>
                </div>
            </div>
        </div>
    </section>
@endsection

