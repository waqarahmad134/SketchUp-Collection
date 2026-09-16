@extends('layouts.app')

@section('content')
    @php
        $faqSchema = [
            '@context' => 'https://schema.org',
            '@type' => 'FAQPage',
            'mainEntity' => [],
        ];
    @endphp
    <section class="py-24">
        <div class="container mx-auto px-4">
            <div class="max-w-3xl mx-auto">
                <h1 class="text-4xl md:text-5xl font-bold font-display text-center mb-4">
                    Frequently Asked <span class="gradient-text">Questions</span>
                </h1>
                <p class="text-muted-foreground text-center mb-12">Everything you need to know about our bundles, downloads and payments.</p>

                @php
                    $faqs = [
                        [
                            'q' => 'What do I get when I buy a bundle?',
                            'a' => 'You get instant access to download all SketchUp models, textures and files included in the bundle. Files are delivered as a ZIP download, and you can re-download them anytime from your account.',
                        ],
                        [
                            'q' => 'How do I download my files after purchase?',
                            'a' => 'Right after payment you receive a confirmation email with download links. You can also find every purchase under My Account > Orders, with lifetime re-download access.',
                        ],
                        [
                            'q' => 'Which SketchUp versions are supported?',
                            'a' => 'Our models are saved in widely compatible SketchUp formats. Each bundle page lists the exact version and file details, so check before buying.',
                        ],
                        [
                            'q' => 'Can I use the models in commercial projects?',
                            'a' => 'Yes. Every purchase includes a commercial license for use in client work and commercial projects. Redistribution or resale of the raw files is not allowed. See our License page for details.',
                        ],
                        [
                            'q' => 'What payment methods do you accept?',
                            'a' => 'We accept international cards via Stripe, Paddle, Lemon Squeezy and Polar, plus JazzCash, Easypaisa and PayPro for customers in Pakistan.',
                        ],
                        [
                            'q' => 'Do you offer refunds?',
                            'a' => 'Because these are instant digital downloads, all sales are final. If your files are corrupted or not as described, contact us within 7 days and we will replace the files or issue a refund. See our Refund Policy.',
                        ],
                        [
                            'q' => 'What are SKP coins?',
                            'a' => 'SKP coins are our reward points. You earn them from daily logins, purchases and referrals, and you can spend them for discounts at checkout.',
                        ],
                        [
                            'q' => 'I lost my download link. What should I do?',
                            'a' => 'Log in and open My Account > Orders. Every order keeps its download links forever. If you checked out as a guest, contact us with your order number.',
                        ],
                    ];
                @endphp

                <div class="space-y-4">
                    @foreach($faqs as $index => $faq)
                        <details class="glass-card rounded-2xl group" {{ $index === 0 ? 'open' : '' }}>
                            <summary class="cursor-pointer list-none p-5 flex items-center justify-between gap-4 font-semibold">
                                {{ $faq['q'] }}
                                <i data-lucide="chevron-down" class="w-5 h-5 shrink-0 text-cyan-400 transition-transform group-open:rotate-180"></i>
                            </summary>
                            <div class="px-5 pb-5 text-muted-foreground">
                                {{ $faq['a'] }}
                            </div>
                        </details>
                    @endforeach
                </div>

                <div class="text-center mt-12">
                    <p class="text-muted-foreground mb-4">Still have questions?</p>
                    <a href="{{ route('contact') }}" class="inline-flex items-center gap-2 px-8 py-4 rounded-xl bg-gradient-to-r from-cyan-500 to-violet-500 text-background font-semibold shadow-lg hover:shadow-xl transition">
                        Contact Us <i data-lucide="arrow-right" class="w-5 h-5"></i>
                    </a>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('head')
    @php
        $faqSchema['mainEntity'] = collect($faqs)->map(fn ($faq) => [
            '@type' => 'Question',
            'name' => $faq['q'],
            'acceptedAnswer' => [
                '@type' => 'Answer',
                'text' => $faq['a'],
            ],
        ])->values()->all();
    @endphp
    <script type="application/ld+json">{!! json_encode($faqSchema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>
@endpush
