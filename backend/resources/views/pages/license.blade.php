@extends('layouts.app')

@section('content')
    @php
        $allowed = [
            'Use in commercial projects',
            'Use in client work',
            'Modify and customize assets',
            'Use in multiple projects',
            'Lifetime access to purchased assets',
        ];

        $notAllowed = [
            'Resell or redistribute assets',
            'Share account access',
            'Create asset libraries for resale',
            'Use assets in products for resale (templates, themes)',
            'Remove copyright notices',
        ];
    @endphp

    <section class="py-24 mesh-gradient relative overflow-hidden">
        <div class="absolute top-0 left-1/2 -translate-x-1/2 w-[800px] h-[400px] bg-gradient-to-b from-violet-500/10 to-transparent rounded-full blur-3xl"></div>
        
        <div class="container mx-auto px-4 relative z-10">
            <div class="text-center mb-16 space-y-4">
                <div class="inline-flex items-center gap-2 glass-card px-4 py-2 rounded-full">
                    <i data-lucide="award" class="w-4 h-4 text-cyan-400"></i>
                    <span class="text-sm text-cyan-400 font-medium">Commercial License</span>
                </div>
                <h1 class="text-4xl md:text-6xl font-bold font-display">
                    License <span class="gradient-text">Agreement</span>
                </h1>
                <p class="text-xl text-muted-foreground max-w-2xl mx-auto">
                    Understand what you can and cannot do with SketchUp Collection assets.
                </p>
            </div>

            <div class="max-w-4xl mx-auto space-y-8">
                <div class="glass-card rounded-3xl p-8">
                    <h2 class="text-2xl font-bold font-display mb-6">What's Included</h2>
                    <p class="text-muted-foreground mb-6">
                        When you purchase assets from SketchUp Collection, you receive a commercial license that allows you to:
                    </p>
                    <div class="space-y-3">
                        @foreach($allowed as $item)
                            <div class="flex items-start gap-3">
                                <div class="w-6 h-6 rounded-full bg-cyan-500/20 flex items-center justify-center flex-shrink-0 mt-0.5">
                                    <i data-lucide="check" class="w-4 h-4 text-cyan-400"></i>
                                </div>
                                <span class="text-muted-foreground">{{ $item }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="glass-card rounded-3xl p-8">
                    <h2 class="text-2xl font-bold font-display mb-6">What's Not Allowed</h2>
                    <p class="text-muted-foreground mb-6">
                        To protect our assets and community, the following are prohibited:
                    </p>
                    <div class="space-y-3">
                        @foreach($notAllowed as $item)
                            <div class="flex items-start gap-3">
                                <div class="w-6 h-6 rounded-full bg-red-500/20 flex items-center justify-center flex-shrink-0 mt-0.5">
                                    <i data-lucide="x" class="w-4 h-4 text-red-400"></i>
                                </div>
                                <span class="text-muted-foreground">{{ $item }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="glass-card rounded-3xl p-8">
                    <h2 class="text-2xl font-bold font-display mb-4">License Types</h2>
                    <div class="space-y-4">
                        <div>
                            <h3 class="text-lg font-semibold mb-2">Standard Commercial License</h3>
                            <p class="text-muted-foreground text-sm">
                                Included with all purchases. Allows commercial use in client projects, presentations, and personal work. Perfect for designers, architects, and 3D artists.
                            </p>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold mb-2">Extended License</h3>
                            <p class="text-muted-foreground text-sm">
                                Available for Enterprise plans. Allows use in products for resale, templates, and larger commercial applications. Contact us for more information.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="glass-card rounded-3xl p-8">
                    <h2 class="text-2xl font-bold font-display mb-4">Questions?</h2>
                    <p class="text-muted-foreground">
                        If you have questions about licensing or need clarification on what's allowed, please contact us at
                        <a href="mailto:license@sketchupcollection.com" class="text-cyan-400 hover:underline"> license@sketchupcollection.com </a>
                    </p>
                </div>
            </div>
        </div>
    </section>
@endsection

