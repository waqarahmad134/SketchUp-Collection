@extends('layouts.app')

@section('content')
    @php
        $discount = $product->discount_percentage ?? ($product->original_price > 0 ? round((1 - $product->price / $product->original_price) * 100) : 0);
        $images = collect($product->images ?? []);
    @endphp

    <div class="container mx-auto px-4 py-6">
        <a href="{{ route('bundles.index') }}" class="inline-flex items-center gap-2 text-muted-foreground hover:text-cyan-400 transition-colors">
            <i data-lucide="arrow-left" class="w-4 h-4"></i>
            Back to Products
        </a>
    </div>

    <section class="py-12 mesh-gradient relative overflow-hidden">
        <div class="absolute top-0 left-1/2 -translate-x-1/2 w-[800px] h-[400px] bg-gradient-to-b from-cyan-500/10 to-transparent rounded-full blur-3xl"></div>
        
        <div class="container mx-auto px-4 relative z-10">
            <div class="grid lg:grid-cols-2 gap-12">
                <div class="space-y-4">
                    <div class="glass-card rounded-3xl overflow-hidden">
                        @if($product->image_url)
                            <img src="{{ $product->image_url }}" alt="{{ $product->title }}" class="w-full h-auto">
                        @else
                            <div class="w-full h-96 bg-gradient-to-br from-cyan-500/20 to-violet-500/20 flex items-center justify-center">
                                <i data-lucide="package" class="w-16 h-16 text-cyan-400/50"></i>
                            </div>
                        @endif
                    </div>
                    @if($images->count() > 1)
                        <div class="grid grid-cols-3 gap-4">
                            @foreach($images->slice(1) as $img)
                                <div class="glass-card rounded-2xl overflow-hidden">
                                    <img src="{{ \Illuminate\Support\Str::startsWith($img, ['http://', 'https://']) ? $img : \Illuminate\Support\Facades\Storage::url($img) }}" alt="{{ $product->title }} extra" class="w-full h-32 object-cover">
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

                <div class="space-y-6">
                    <div>
                        <div class="flex items-center gap-3 mb-4">
                            @if($product->is_bundle)
                                <span class="bg-violet-500/20 text-violet-400 text-sm font-bold px-4 py-1.5 rounded-full flex items-center gap-2">
                                    <i data-lucide="package" class="w-4 h-4"></i>
                                    Bundle
                                </span>
                            @endif
                            @if($discount > 0)
                                <span class="bg-cyan-500/20 text-cyan-400 text-sm font-bold px-4 py-1.5 rounded-full">
                                    {{ $discount }}% OFF
                                </span>
                            @endif
                        </div>
                        <h1 class="text-4xl md:text-5xl font-bold font-display mb-4">
                            {{ $product->title }}
                        </h1>
                        @if($product->full_description || $product->description)
                            <p class="text-xl text-muted-foreground mb-6">
                                {{ $product->full_description ?? $product->description }}
                            </p>
                        @endif
                    </div>

                    <div class="glass-card rounded-3xl p-6">
                        <div class="flex items-baseline gap-4 mb-4">
                            <span class="text-5xl font-bold gradient-text">${{ number_format($product->price, 2) }}</span>
                            @if($product->original_price)
                                <span class="text-2xl text-muted-foreground line-through">
                                    ${{ number_format($product->original_price, 2) }}
                                </span>
                            @endif
                        </div>
                        <p class="text-sm text-muted-foreground mb-6">
                            One-time payment • Lifetime access • Commercial license included
                        </p>
                        <div class="space-y-3">
                            <button class="w-full px-4 py-3 rounded-xl bg-gradient-to-r from-cyan-500 to-violet-500 text-background font-semibold shadow-lg hover:shadow-xl transition inline-flex items-center justify-center gap-2">
                                <i data-lucide="shopping-cart" class="w-5 h-5"></i>
                                Add to Cart
                            </button>
                            <button class="w-full px-4 py-3 rounded-xl border border-border text-foreground font-semibold hover:border-foreground transition inline-flex items-center justify-center gap-2">
                                <i data-lucide="download" class="w-5 h-5"></i>
                                Instant Download
                            </button>
                        </div>
                    </div>

                    <div class="glass-card rounded-3xl p-6">
                        <h3 class="font-semibold mb-4">Product Details</h3>
                        <div class="space-y-3 text-sm">
                            @if($product->file_size)
                                <div class="flex items-center justify-between">
                                    <span class="text-muted-foreground">File Size</span>
                                    <span class="font-medium">{{ $product->file_size }}</span>
                                </div>
                            @endif
                            @if($product->file_count)
                                <div class="flex items-center justify-between">
                                    <span class="text-muted-foreground">File Count</span>
                                    <span class="font-medium">{{ $product->file_count }} files</span>
                                </div>
                            @endif
                            @if($product->category)
                                <div class="flex items-center justify-between">
                                    <span class="text-muted-foreground">Category</span>
                                    <span class="font-medium capitalize">{{ $product->category }}</span>
                                </div>
                            @endif
                            <div class="flex items-center justify-between">
                                <span class="text-muted-foreground">License</span>
                                <span class="font-medium">Commercial</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    @if($product->features)
        <section class="py-16 bg-background">
            <div class="container mx-auto px-4">
                <div class="max-w-4xl mx-auto">
                    <h2 class="text-3xl font-bold font-display mb-8">What's Included</h2>
                    <div class="grid md:grid-cols-2 gap-4">
                        @foreach($product->features as $feature)
                            <div class="flex items-start gap-3 glass-card rounded-2xl p-4">
                                <div class="w-6 h-6 rounded-full bg-cyan-500/20 flex items-center justify-center flex-shrink-0 mt-0.5">
                                    <i data-lucide="check" class="w-4 h-4 text-cyan-400"></i>
                                </div>
                                <span class="text-muted-foreground">{{ $feature }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </section>
    @endif

    @if($product->is_bundle && $product->included_products)
        <section class="py-16 bg-card">
            <div class="container mx-auto px-4">
                <div class="max-w-4xl mx-auto">
                    <h2 class="text-3xl font-bold font-display mb-8">
                        This Bundle Includes
                    </h2>
                    <p class="text-muted-foreground mb-6">
                        This bundle combines {{ count($product->included_products) }} products for maximum value:
                    </p>
                    <div class="grid md:grid-cols-2 gap-4">
                        @foreach($product->included_products as $includedId)
                            @php $included = $allProducts->get($includedId); @endphp
                            @if($included)
                                <div class="glass-card rounded-2xl p-4 flex items-center gap-4">
                                    <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-cyan-500/20 to-violet-500/20 flex items-center justify-center flex-shrink-0">
                                        <i data-lucide="file-box" class="w-6 h-6 text-cyan-400"></i>
                                    </div>
                                    <div>
                                        <h4 class="font-semibold">{{ $included->title }}</h4>
                                        <p class="text-sm text-muted-foreground">
                                            {{ $included->file_count }} files • {{ $included->file_size }}
                                        </p>
                                    </div>
                                </div>
                            @endif
                        @endforeach
                    </div>
                </div>
            </div>
        </section>
    @endif

    <section class="py-16 mesh-gradient">
        <div class="container mx-auto px-4">
            <div class="max-w-4xl mx-auto">
                <h2 class="text-3xl font-bold font-display mb-8 text-center">
                    Why Choose This {{ $product->is_bundle ? 'Bundle' : 'Product' }}?
                </h2>
                <div class="grid md:grid-cols-3 gap-6">
                    <div class="glass-card rounded-3xl p-6 text-center">
                        <div class="w-16 h-16 mx-auto mb-4 rounded-2xl bg-gradient-to-br from-cyan-500/20 to-violet-500/20 flex items-center justify-center">
                            <i data-lucide="zap" class="w-8 h-8 text-cyan-400"></i>
                        </div>
                        <h3 class="font-semibold mb-2">Instant Download</h3>
                        <p class="text-sm text-muted-foreground">
                            Get immediate access via Google Drive or Dropbox
                        </p>
                    </div>
                    <div class="glass-card rounded-3xl p-6 text-center">
                        <div class="w-16 h-16 mx-auto mb-4 rounded-2xl bg-gradient-to-br from-cyan-500/20 to-violet-500/20 flex items-center justify-center">
                            <i data-lucide="shield" class="w-8 h-8 text-cyan-400"></i>
                        </div>
                        <h3 class="font-semibold mb-2">Commercial License</h3>
                        <p class="text-sm text-muted-foreground">
                            Use in client projects without restrictions
                        </p>
                    </div>
                    <div class="glass-card rounded-3xl p-6 text-center">
                        <div class="w-16 h-16 mx-auto mb-4 rounded-2xl bg-gradient-to-br from-cyan-500/20 to-violet-500/20 flex items-center justify-center">
                            <i data-lucide="download" class="w-8 h-8 text-cyan-400"></i>
                        </div>
                        <h3 class="font-semibold mb-2">Lifetime Access</h3>
                        <p class="text-sm text-muted-foreground">
                            Download anytime, even after updates
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

