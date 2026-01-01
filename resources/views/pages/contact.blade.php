@extends('layouts.app')

@section('content')
    <section class="py-24 mesh-gradient relative overflow-hidden">
        <div class="absolute top-0 left-1/2 -translate-x-1/2 w-[800px] h-[400px] bg-gradient-to-b from-cyan-500/10 to-transparent rounded-full blur-3xl"></div>
        
        <div class="container mx-auto px-4 relative z-10">
            <div class="text-center mb-16 space-y-4">
                <h1 class="text-4xl md:text-6xl font-bold font-display">
                    Get in <span class="gradient-text">Touch</span>
                </h1>
                <p class="text-xl text-muted-foreground max-w-2xl mx-auto">
                    Have questions? We're here to help. Reach out to our team anytime.
                </p>
            </div>

            <div class="grid md:grid-cols-2 gap-12 max-w-6xl mx-auto">
                <div class="space-y-8">
                    <div>
                        <h2 class="text-2xl font-bold font-display mb-6">Contact Information</h2>
                        <div class="space-y-6">
                            <div class="flex items-start gap-4">
                                <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-cyan-500/20 to-violet-500/20 flex items-center justify-center flex-shrink-0">
                                    <i data-lucide="mail" class="w-6 h-6 text-cyan-400"></i>
                                </div>
                                <div>
                                    <h3 class="font-semibold mb-1">Email</h3>
                                    <p class="text-muted-foreground">support@sketchupcollection.com</p>
                                </div>
                            </div>
                            <div class="flex items-start gap-4">
                                <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-cyan-500/20 to-violet-500/20 flex items-center justify-center flex-shrink-0">
                                    <i data-lucide="phone" class="w-6 h-6 text-cyan-400"></i>
                                </div>
                                <div>
                                    <h3 class="font-semibold mb-1">Phone</h3>
                                    <p class="text-muted-foreground">+1 (555) 123-4567</p>
                                </div>
                            </div>
                            <div class="flex items-start gap-4">
                                <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-cyan-500/20 to-violet-500/20 flex items-center justify-center flex-shrink-0">
                                    <i data-lucide="map-pin" class="w-6 h-6 text-cyan-400"></i>
                                </div>
                                <div>
                                    <h3 class="font-semibold mb-1">Address</h3>
                                    <p class="text-muted-foreground">
                                        123 Design Street<br>
                                        Creative City, CC 12345
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="glass-card rounded-3xl p-6">
                        <h3 class="font-semibold mb-3">Response Time</h3>
                        <p class="text-sm text-muted-foreground">
                            We typically respond within 24 hours during business days. For urgent matters, please call us directly.
                        </p>
                    </div>
                </div>

                <div class="glass-card rounded-3xl p-8">
                    <h2 class="text-2xl font-bold font-display mb-6">Send us a Message</h2>
                    <form class="space-y-6">
                        <div class="space-y-2">
                            <label for="name" class="font-medium">Name</label>
                            <input id="name" name="name" class="w-full px-4 py-3 rounded-xl bg-card border border-border focus:border-cyan-500 focus:ring-2 focus:ring-cyan-500/30 outline-none" placeholder="Your name">
                        </div>
                        <div class="space-y-2">
                            <label for="email" class="font-medium">Email</label>
                            <input id="email" type="email" name="email" class="w-full px-4 py-3 rounded-xl bg-card border border-border focus:border-cyan-500 focus:ring-2 focus:ring-cyan-500/30 outline-none" placeholder="your@email.com">
                        </div>
                        <div class="space-y-2">
                            <label for="subject" class="font-medium">Subject</label>
                            <input id="subject" name="subject" class="w-full px-4 py-3 rounded-xl bg-card border border-border focus:border-cyan-500 focus:ring-2 focus:ring-cyan-500/30 outline-none" placeholder="What's this about?">
                        </div>
                        <div class="space-y-2">
                            <label for="message" class="font-medium">Message</label>
                            <textarea id="message" name="message" rows="6" class="w-full px-4 py-3 rounded-xl bg-card border border-border focus:border-cyan-500 focus:ring-2 focus:ring-cyan-500/30 outline-none" placeholder="Tell us more..."></textarea>
                        </div>
                        <button type="button" class="w-full px-4 py-3 rounded-xl bg-gradient-to-r from-cyan-500 to-violet-500 text-background font-semibold shadow-lg hover:shadow-xl transition inline-flex items-center justify-center gap-2">
                            <i data-lucide="send" class="w-5 h-5"></i>
                            Send Message
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </section>
@endsection

