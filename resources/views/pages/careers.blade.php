@extends('layouts.app')

@section('content')
    @php
        $benefits = [
            'Competitive salary & equity',
            'Remote-first culture',
            'Flexible working hours',
            'Health & dental insurance',
            'Learning & development budget',
            'Unlimited PTO',
        ];

        $openPositions = [
            ['title' => 'Senior 3D Modeler', 'department' => 'Content', 'type' => 'Full-time', 'location' => 'Remote'],
            ['title' => 'Frontend Developer', 'department' => 'Engineering', 'type' => 'Full-time', 'location' => 'Remote'],
            ['title' => 'Content Marketing Manager', 'department' => 'Marketing', 'type' => 'Full-time', 'location' => 'Remote'],
        ];
    @endphp

    <section class="py-24 mesh-gradient relative overflow-hidden">
        <div class="absolute top-0 left-1/2 -translate-x-1/2 w-[800px] h-[400px] bg-gradient-to-b from-cyan-500/10 to-transparent rounded-full blur-3xl"></div>
        
        <div class="container mx-auto px-4 relative z-10">
            <div class="text-center mb-16 space-y-4">
                <div class="inline-flex items-center gap-2 glass-card px-4 py-2 rounded-full">
                    <i data-lucide="briefcase" class="w-4 h-4 text-cyan-400"></i>
                    <span class="text-sm text-cyan-400 font-medium">Join Our Team</span>
                </div>
                <h1 class="text-4xl md:text-6xl font-bold font-display">
                    Careers at <span class="gradient-text">SketchUp Collection</span>
                </h1>
                <p class="text-xl text-muted-foreground max-w-2xl mx-auto">
                    Help us build the future of 3D asset distribution for designers worldwide.
                </p>
            </div>

            <div class="grid md:grid-cols-3 gap-6 mb-16">
                <div class="glass-card rounded-3xl p-6 text-center">
                    <i data-lucide="users" class="w-12 h-12 text-cyan-400 mx-auto mb-4"></i>
                    <h3 class="text-xl font-bold font-display mb-2">Great Team</h3>
                    <p class="text-sm text-muted-foreground">Work with passionate designers and developers</p>
                </div>
                <div class="glass-card rounded-3xl p-6 text-center">
                    <i data-lucide="zap" class="w-12 h-12 text-violet-400 mx-auto mb-4"></i>
                    <h3 class="text-xl font-bold font-display mb-2">Fast Growth</h3>
                    <p class="text-sm text-muted-foreground">Be part of a rapidly growing startup</p>
                </div>
                <div class="glass-card rounded-3xl p-6 text-center">
                    <i data-lucide="heart" class="w-12 h-12 text-cyan-400 mx-auto mb-4"></i>
                    <h3 class="text-xl font-bold font-display mb-2">Make Impact</h3>
                    <p class="text-sm text-muted-foreground">Help thousands of designers worldwide</p>
                </div>
            </div>

            <div class="max-w-4xl mx-auto space-y-8">
                <div>
                    <h2 class="text-3xl font-bold font-display mb-6">Open Positions</h2>
                    <div class="space-y-4">
                        @foreach($openPositions as $position)
                            <div class="glass-card rounded-3xl p-6 hover-lift">
                                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                                    <div>
                                        <h3 class="text-xl font-bold font-display mb-2">{{ $position['title'] }}</h3>
                                        <div class="flex flex-wrap gap-3 text-sm text-muted-foreground">
                                            <span>{{ $position['department'] }}</span>
                                            <span>•</span>
                                            <span>{{ $position['type'] }}</span>
                                            <span>•</span>
                                            <span>{{ $position['location'] }}</span>
                                        </div>
                                    </div>
                                    <button class="px-4 py-2 rounded-xl border border-border hover:border-foreground transition font-semibold">
                                        Apply Now
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="glass-card rounded-3xl p-8">
                    <h2 class="text-2xl font-bold font-display mb-6">Benefits & Perks</h2>
                    <div class="grid md:grid-cols-2 gap-4">
                        @foreach($benefits as $benefit)
                            <div class="flex items-center gap-3">
                                <div class="w-2 h-2 rounded-full bg-cyan-400"></div>
                                <span class="text-muted-foreground">{{ $benefit }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

