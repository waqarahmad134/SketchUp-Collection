@extends('layouts.app')

@section('content')
    <section class="py-24 mesh-gradient relative overflow-hidden">
        <div class="absolute top-0 left-1/2 -translate-x-1/2 w-[800px] h-[400px] bg-gradient-to-b from-cyan-500/10 to-transparent rounded-full blur-3xl"></div>
        
        <div class="container mx-auto px-4 relative z-10">
            <div class="text-center mb-16 space-y-4">
                <div class="inline-flex items-center gap-2 glass-card px-4 py-2 rounded-full">
                    <i data-lucide="shield" class="w-4 h-4 text-cyan-400"></i>
                    <span class="text-sm text-cyan-400 font-medium">Legal</span>
                </div>
                <h1 class="text-4xl md:text-6xl font-bold font-display">
                    Privacy <span class="gradient-text">Policy</span>
                </h1>
                <p class="text-muted-foreground">Last updated: December 2024</p>
            </div>

            <div class="max-w-4xl mx-auto space-y-8">
                <div class="glass-card rounded-3xl p-8">
                    <h2 class="text-2xl font-bold font-display mb-4">1. Information We Collect</h2>
                    <p class="text-muted-foreground mb-4">
                        We collect information that you provide directly to us, including:
                    </p>
                    <ul class="list-disc list-inside space-y-2 text-muted-foreground ml-4">
                        <li>Name and email address when you create an account</li>
                        <li>Payment information when you make a purchase</li>
                        <li>Communication data when you contact our support team</li>
                        <li>Usage data and preferences to improve our services</li>
                    </ul>
                </div>

                <div class="glass-card rounded-3xl p-8">
                    <h2 class="text-2xl font-bold font-display mb-4">2. How We Use Your Information</h2>
                    <p class="text-muted-foreground mb-4">
                        We use the information we collect to:
                    </p>
                    <ul class="list-disc list-inside space-y-2 text-muted-foreground ml-4">
                        <li>Process and fulfill your orders</li>
                        <li>Send you important updates about your account</li>
                        <li>Respond to your inquiries and provide customer support</li>
                        <li>Improve our website and services</li>
                        <li>Send marketing communications (with your consent)</li>
                    </ul>
                </div>

                <div class="glass-card rounded-3xl p-8">
                    <h2 class="text-2xl font-bold font-display mb-4">3. Data Security</h2>
                    <p class="text-muted-foreground">
                        We implement appropriate technical and organizational measures to protect your personal information against unauthorized access, alteration, disclosure, or destruction. However, no method of transmission over the Internet is 100% secure.
                    </p>
                </div>

                <div class="glass-card rounded-3xl p-8">
                    <h2 class="text-2xl font-bold font-display mb-4">4. Your Rights</h2>
                    <p class="text-muted-foreground mb-4">
                        You have the right to:
                    </p>
                    <ul class="list-disc list-inside space-y-2 text-muted-foreground ml-4">
                        <li>Access your personal information</li>
                        <li>Correct inaccurate data</li>
                        <li>Request deletion of your data</li>
                        <li>Opt-out of marketing communications</li>
                        <li>Data portability</li>
                    </ul>
                </div>

                <div class="glass-card rounded-3xl p-8">
                    <h2 class="text-2xl font-bold font-display mb-4">5. Contact Us</h2>
                    <p class="text-muted-foreground">
                        If you have questions about this Privacy Policy, please contact us at
                        <a href="mailto:privacy@sketchupcollection.com" class="text-cyan-400 hover:underline"> privacy@sketchupcollection.com</a>
                    </p>
                </div>
            </div>
        </div>
    </section>
@endsection

