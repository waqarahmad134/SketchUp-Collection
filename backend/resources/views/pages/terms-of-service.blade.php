@extends('layouts.app')

@section('content')
    <section class="py-24 mesh-gradient relative overflow-hidden">
        <div class="absolute top-0 left-1/2 -translate-x-1/2 w-[800px] h-[400px] bg-gradient-to-b from-cyan-500/10 to-transparent rounded-full blur-3xl"></div>
        
        <div class="container mx-auto px-4 relative z-10">
            <div class="text-center mb-16 space-y-4">
                <div class="inline-flex items-center gap-2 glass-card px-4 py-2 rounded-full">
                    <i data-lucide="file-text" class="w-4 h-4 text-cyan-400"></i>
                    <span class="text-sm text-cyan-400 font-medium">Legal</span>
                </div>
                <h1 class="text-4xl md:text-6xl font-bold font-display">
                    Terms of <span class="gradient-text">Service</span>
                </h1>
                <p class="text-muted-foreground">Last updated: December 2024</p>
            </div>

            <div class="max-w-4xl mx-auto space-y-8">
                <div class="glass-card rounded-3xl p-8">
                    <h2 class="text-2xl font-bold font-display mb-4">1. Acceptance of Terms</h2>
                    <p class="text-muted-foreground">
                        By accessing and using 3DAssetHub, you accept and agree to be bound by these Terms of Service. If you do not agree to these terms, please do not use our service.
                    </p>
                </div>

                <div class="glass-card rounded-3xl p-8">
                    <h2 class="text-2xl font-bold font-display mb-4">2. Use License</h2>
                    <p class="text-muted-foreground mb-4">
                        When you purchase assets from 3DAssetHub, you receive:
                    </p>
                    <ul class="list-disc list-inside space-y-2 text-muted-foreground ml-4">
                        <li>A commercial license to use the assets in your projects</li>
                        <li>Lifetime access to purchased assets</li>
                        <li>The right to use assets in client work and commercial projects</li>
                        <li>You may NOT redistribute, resell, or share purchased assets</li>
                    </ul>
                </div>

                <div class="glass-card rounded-3xl p-8">
                    <h2 class="text-2xl font-bold font-display mb-4">3. Payment Terms</h2>
                    <p class="text-muted-foreground mb-4">
                        All purchases are one-time payments that grant you lifetime access. We accept major credit cards and payment processors. Refunds are available within 30 days of purchase if you're not satisfied.
                    </p>
                </div>

                <div class="glass-card rounded-3xl p-8">
                    <h2 class="text-2xl font-bold font-display mb-4">4. User Accounts</h2>
                    <p class="text-muted-foreground mb-4">
                        You are responsible for:
                    </p>
                    <ul class="list-disc list-inside space-y-2 text-muted-foreground ml-4">
                        <li>Maintaining the confidentiality of your account credentials</li>
                        <li>All activities that occur under your account</li>
                        <li>Providing accurate and complete information</li>
                    </ul>
                </div>

                <div class="glass-card rounded-3xl p-8">
                    <h2 class="text-2xl font-bold font-display mb-4">5. Prohibited Uses</h2>
                    <p class="text-muted-foreground mb-4">
                        You may not:
                    </p>
                    <ul class="list-disc list-inside space-y-2 text-muted-foreground ml-4">
                        <li>Resell or redistribute purchased assets</li>
                        <li>Share your account credentials with others</li>
                        <li>Use assets in ways that violate applicable laws</li>
                        <li>Reverse engineer or extract assets for redistribution</li>
                    </ul>
                </div>

                <div class="glass-card rounded-3xl p-8">
                    <h2 class="text-2xl font-bold font-display mb-4">6. Limitation of Liability</h2>
                    <p class="text-muted-foreground">
                        3DAssetHub shall not be liable for any indirect, incidental, special, or consequential damages arising from your use of our service or assets.
                    </p>
                </div>

                <div class="glass-card rounded-3xl p-8">
                    <h2 class="text-2xl font-bold font-display mb-4">7. Changes to Terms</h2>
                    <p class="text-muted-foreground">
                        We reserve the right to modify these terms at any time. Continued use of our service after changes constitutes acceptance of the new terms.
                    </p>
                </div>
            </div>
        </div>
    </section>
@endsection

