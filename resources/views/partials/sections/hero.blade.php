<section class="relative min-h-screen mesh-gradient overflow-hidden">
    <div class="absolute inset-0 overflow-hidden pointer-events-none">
        <div class="absolute top-20 left-10 w-72 h-72 bg-cyan-500/10 rounded-full blur-3xl animate-float"></div>
        <div class="absolute bottom-20 right-10 w-96 h-96 bg-violet-500/10 rounded-full blur-3xl animate-float-delayed"></div>
        <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[600px] h-[600px] bg-cyan-500/5 rounded-full blur-3xl animate-pulse-glow"></div>
    </div>

    <div class="container relative z-10 mx-auto px-4 pt-32 pb-20">
        <div class="grid lg:grid-cols-2 gap-12 items-center">
            <div class="space-y-8 animate-slide-in-up">
                <div class="inline-flex items-center gap-2 glass-card px-4 py-2 rounded-full">
                    <i data-lucide="sparkles" class="w-4 h-4 text-cyan-400"></i>
                    <span class="text-sm text-muted-foreground">Premium 2026 Collection</span>
                    <span class="bg-gradient-to-r from-cyan-500 to-violet-500 text-background text-xs font-bold px-2 py-0.5 rounded-full">
                        50% OFF
                    </span>
                </div>

                <h1 class="text-5xl md:text-6xl lg:text-7xl font-bold font-display leading-tight">
                    <span class="text-foreground">Premium</span>
                    <span class="gradient-text"> Sketchup</span><br>
                    <span class="text-foreground">Model Collection</span>
                </h1>

                <p class="text-xl text-muted-foreground max-w-lg">
                Premium SketchUp model bundle collection with high quality exterior, interior, landscape and misc 3D models. Ready to use for architects and designers.
                </p>

                <div class="glass-card p-6 rounded-2xl space-y-4 max-w-md">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <i data-lucide="box" class="w-5 h-5 text-cyan-400"></i>
                            <span class="font-medium">1TB+ Premium SKP Bundle</span>
                        </div>
                        <div class="text-right">
                            <span class="text-2xl font-bold gradient-text">$49</span>
                            <span class="text-muted-foreground line-through ml-2">$98</span>
                        </div>
                    </div>
                    <div class="flex items-center gap-3 text-muted-foreground">
                        <i data-lucide="download" class="w-4 h-4"></i>
                        <span>500GB Furniture SKP Models</span>
                    </div>
                    <div class="flex items-center gap-3 text-muted-foreground">
                        <i data-lucide="zap" class="w-4 h-4"></i>
                        <span>10GB SKM Textures</span>
                    </div>
                    <div class="flex items-center gap-3 text-muted-foreground">
                        <i data-lucide="sparkles" class="w-4 h-4"></i>
                        <span>60GB Designer-Exclusive Bonus Pack</span>
                    </div>
                </div>

                <div class="flex flex-wrap gap-4">
                    <a href="#bundles" class="px-6 py-3 rounded-xl bg-gradient-to-r from-cyan-500 to-violet-500 text-background font-semibold shadow-lg hover:shadow-xl transition">
                        Get Started Now
                    </a>
                    <a href="#bundles" class="px-6 py-3 rounded-xl border border-border text-foreground font-semibold hover:border-foreground transition">
                        Preview Assets
                    </a>
                </div>

                <div class="flex items-center gap-4 pt-4">
                    <div class="flex -space-x-3">
                        @foreach([1,2,3,4] as $i)
                            <div class="w-10 h-10 rounded-full bg-gradient-to-br from-cyan-500 to-violet-500 border-2 border-background flex items-center justify-center text-xs font-bold">
                                {{ chr(64 + $i) }}
                            </div>
                        @endforeach
                    </div>
                    <div>
                        <div class="flex items-center gap-1">
                            @foreach([1,2,3,4,5] as $i)
                                <i data-lucide="star" class="w-4 h-4 text-cyan-400" style="fill: currentColor;"></i>
                            @endforeach
                            <span class="font-semibold ml-1">5.0</span>
                        </div>
                        <p class="text-sm text-muted-foreground">10,000+ happy designers</p>
                    </div>
                </div>
            </div>

            <div class="relative animate-slide-in-up animation-delay-400">
                <div class="relative">
                    <div class="absolute inset-0 bg-gradient-to-r from-cyan-500/20 to-violet-500/20 rounded-3xl blur-2xl"></div>
                    <img
                        src="{{ asset('assets/hero-3d-workspace.jpg') }}"
                        alt="3D Asset Workspace"
                        class="relative rounded-3xl shadow-2xl border border-border hover-lift w-full h-auto"
                    >

                    <div class="absolute -bottom-6 -left-6 glass-card p-4 rounded-2xl animate-float">
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-cyan-500 to-violet-500 flex items-center justify-center">
                                <i data-lucide="users" class="w-6 h-6 text-background"></i>
                            </div>
                            <div>
                                <p class="font-bold text-lg">50K+</p>
                                <p class="text-sm text-muted-foreground">Active Users</p>
                            </div>
                        </div>
                    </div>

                    <div class="absolute -top-4 -right-4 glass-card p-4 rounded-2xl animate-float-delayed">
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-violet-500 to-cyan-500 flex items-center justify-center">
                                <i data-lucide="download" class="w-6 h-6 text-background"></i>
                            </div>
                            <div>
                                <p class="font-bold text-lg">1TB+</p>
                                <p class="text-sm text-muted-foreground">Assets</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

