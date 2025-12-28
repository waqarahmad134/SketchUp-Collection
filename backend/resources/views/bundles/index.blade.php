@extends('layouts.app')

@section('content')
    <section class="py-24 bg-background relative overflow-hidden">
        <div class="absolute top-0 left-1/2 -translate-x-1/2 w-[800px] h-[400px] bg-gradient-to-b from-cyan-500/5 to-transparent rounded-full blur-3xl"></div>

        <div class="container mx-auto px-4 relative z-10">
            <div class="text-center mb-16 space-y-4">
                <span class="inline-block glass-card px-4 py-2 rounded-full text-sm text-cyan-400 font-medium">
                    Products & Bundles
                </span>
                <h1 class="text-4xl md:text-6xl font-bold font-display">
                    Our <span class="gradient-text">Products</span>
                </h1>
                <p class="text-xl text-muted-foreground max-w-3xl mx-auto">
                    Browse our collection of premium 3D assets. <strong>Bundles</strong> are combinations of multiple products
                    offered at a discounted price, while <strong>single products</strong> are individual asset packs.
                </p>
            </div>

            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8 mb-12">
                @forelse($products as $index => $product)
                    @php
                        $discount = $product->discount_percentage ?? ($product->original_price > 0 ? round((1 - $product->price / $product->original_price) * 100) : 0);
                    @endphp
                    <div class="glass-card rounded-3xl overflow-hidden hover-lift group animate-slide-in-up" style="animation-delay: {{ $index * 0.1 }}s">
                        <div class="relative overflow-hidden">
                            @if($product->image_url)
                                <img src="{{ $product->image_url }}" alt="{{ $product->title }}" class="w-full h-64 object-cover transition-transform duration-700 group-hover:scale-110">
                            @else
                                <div class="w-full h-64 bg-gradient-to-br from-cyan-500/20 to-violet-500/20 flex items-center justify-center">
                                    <i data-lucide="package" class="w-16 h-16 text-cyan-400/50"></i>
                                </div>
                            @endif
                            <div class="absolute inset-0 bg-gradient-to-t from-background/90 via-background/20 to-transparent"></div>
                            <div class="absolute top-4 left-4 flex flex-col gap-2">
                                @if($product->is_bundle)
                                    <span class="bg-violet-500/20 text-violet-400 text-xs font-bold px-3 py-1 rounded-full flex items-center gap-1">
                                        <i data-lucide="package" class="w-3 h-3"></i>
                                        Bundle
                                    </span>
                                @endif
                            </div>
                            <div class="absolute bottom-4 left-4 right-4">
                                <h3 class="text-xl font-bold font-display mb-1">{{ $product->title }}</h3>
                                @if($product->description)
                                    <p class="text-sm text-muted-foreground line-clamp-2">{{ $product->description }}</p>
                                @endif
                            </div>
                        </div>
                        <div class="p-6 space-y-4">
                            <div class="flex items-center justify-between">
                                <div>
                                    <span class="text-3xl font-bold gradient-text">${{ number_format($product->price, 2) }}</span>
                                    @if($product->original_price)
                                        <span class="text-muted-foreground line-through ml-2">${{ number_format($product->original_price, 2) }}</span>
                                    @endif
                                </div>
                                @if($discount > 0)
                                    <span class="bg-cyan-500/20 text-cyan-400 text-xs font-bold px-3 py-1 rounded-full">
                                        {{ $discount }}% OFF
                                    </span>
                                @endif
                            </div>
                            <div class="flex items-center gap-2 text-xs text-muted-foreground">
                                @if($product->file_size)
                                    <span>{{ $product->file_size }}</span>
                                    <span>•</span>
                                @endif
                                @if($product->file_count)
                                    <span>{{ $product->file_count }} files</span>
                                @endif
                            </div>
                            <a href="{{ route('bundles.show', $product->slug) }}" class="w-full inline-flex items-center justify-center gap-2 px-4 py-2 rounded-xl bg-gradient-to-r from-cyan-500 to-violet-500 text-background font-semibold shadow-lg hover:shadow-xl transition group/btn">
                                View {{ $product->is_bundle ? 'Bundle' : 'Product' }}
                                <i data-lucide="arrow-right" class="w-4 h-4 transition-transform group-hover/btn:translate-x-1"></i>
                            </a>
                        </div>
                    </div>
                @empty
                    <div class="col-span-3 text-center text-muted-foreground">
                        No products available yet. Add products in the admin to showcase them here.
                    </div>
                @endforelse
            </div>
        </div>
    </section>
@endsection

