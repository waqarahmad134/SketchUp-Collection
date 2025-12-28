@extends('layouts.app')

@section('content')
    @php
        $pricingPlans = [
            [
                'name' => 'Starter',
                'price' => '$29',
                'description' => 'Perfect for individual designers',
                'features' => [
                    '100+ Premium SKP Models',
                    '5GB Textures Library',
                    'Commercial License',
                    'Email Support',
                    'Lifetime Access',
                ],
                'popular' => false,
            ],
            [
                'name' => 'Professional',
                'price' => '$49',
                'description' => 'Best for professional designers',
                'features' => [
                    '1TB+ Premium SKP Bundle',
                    '500GB Furniture Models',
                    '10GB SKM Textures',
                    '60GB Bonus Pack',
                    'Priority Support',
                    'Commercial License',
                    'Lifetime Access',
                ],
                'popular' => true,
            ],
            [
                'name' => 'Enterprise',
                'price' => '$99',
                'description' => 'For teams and agencies',
                'features' => [
                    'Everything in Professional',
                    'Unlimited Downloads',
                    'Team Collaboration',
                    'Custom Asset Requests',
                    'Dedicated Support',
                    'White-label License',
                    'Early Access to New Assets',
                ],
                'popular' => false,
            ],
        ];
    @endphp

    <section class="py-24 mesh-gradient relative overflow-hidden">
        <div class="absolute top-0 left-1/2 -translate-x-1/2 w-[800px] h-[400px] bg-gradient-to-b from-cyan-500/10 to-transparent rounded-full blur-3xl"></div>
        
        <div class="container mx-auto px-4 relative z-10">
            <div class="text-center mb-16 space-y-4">
                <span class="inline-block glass-card px-4 py-2 rounded-full text-sm text-cyan-400 font-medium">
                    Pricing Plans
                </span>
                <h1 class="text-4xl md:text-6xl font-bold font-display">
                    Choose Your <span class="gradient-text">Plan</span>
                </h1>
                <p class="text-xl text-muted-foreground max-w-2xl mx-auto">
                    Flexible pricing options for designers of all levels. All plans include lifetime access.
                </p>
            </div>

            <div class="grid md:grid-cols-3 gap-8 max-w-6xl mx-auto">
                @foreach($pricingPlans as $plan)
                    <div class="glass-card rounded-3xl p-8 hover-lift relative {{ $plan['popular'] ? 'border-2 border-cyan-500/50' : '' }}">
                        @if($plan['popular'])
                            <div class="absolute -top-4 left-1/2 -translate-x-1/2">
                                <span class="bg-gradient-to-r from-cyan-500 to-violet-500 text-background text-xs font-bold px-4 py-1 rounded-full">
                                    Most Popular
                                </span>
                            </div>
                        @endif

                        <div class="space-y-6">
                            <div>
                                <h3 class="text-2xl font-bold font-display mb-2">{{ $plan['name'] }}</h3>
                                <p class="text-muted-foreground text-sm">{{ $plan['description'] }}</p>
                            </div>
                            
                            <div>
                                <span class="text-5xl font-bold gradient-text">{{ $plan['price'] }}</span>
                                <span class="text-muted-foreground ml-2">one-time</span>
                            </div>

                            <ul class="space-y-3">
                                @foreach($plan['features'] as $feature)
                                    <li class="flex items-start gap-3">
                                        <i data-lucide="check" class="w-5 h-5 text-cyan-400 flex-shrink-0 mt-0.5"></i>
                                        <span class="text-sm">{{ $feature }}</span>
                                    </li>
                                @endforeach
                            </ul>

                            <a class="w-full inline-flex justify-center items-center px-4 py-3 rounded-xl {{ $plan['popular'] ? 'bg-gradient-to-r from-cyan-500 to-violet-500 text-background' : 'border border-border text-foreground' }} font-semibold shadow-lg hover:shadow-xl transition">
                                Get Started
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="mt-16 text-center">
                <p class="text-muted-foreground mb-4">All plans include:</p>
                <div class="flex flex-wrap justify-center gap-6">
                    <div class="flex items-center gap-2 glass-card px-4 py-2 rounded-full">
                        <i data-lucide="zap" class="w-4 h-4 text-cyan-400"></i>
                        <span class="text-sm">Instant Download</span>
                    </div>
                    <div class="flex items-center gap-2 glass-card px-4 py-2 rounded-full">
                        <i data-lucide="sparkles" class="w-4 h-4 text-violet-400"></i>
                        <span class="text-sm">Regular Updates</span>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

