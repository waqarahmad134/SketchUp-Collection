@extends('layouts.app')

@section('content')
    <section class="py-16 bg-background">
        <div class="container mx-auto px-4">
            <div class="glass-card rounded-3xl p-8 mb-10 flex flex-col md:flex-row gap-6 items-center">
                <img src="{{ $avatar }}" alt="{{ $user->name }}" class="w-20 h-20 rounded-full border border-border">
                <div class="flex-1 text-center md:text-left space-y-2">
                    <h1 class="text-3xl font-bold font-display">{{ $user->name }}</h1>
                    @if($user->email)
                        <p class="text-sm text-muted-foreground">{{ $user->email }}</p>
                    @endif
                    <div class="flex flex-wrap gap-3 justify-center md:justify-start text-sm text-muted-foreground">
                        <span class="inline-flex items-center gap-2">
                            <i data-lucide="upload" class="w-4 h-4"></i>
                            {{ $products->count() }} uploads
                        </span>
                        <span class="inline-flex items-center gap-2">
                            <i data-lucide="calendar" class="w-4 h-4"></i>
                            Joined {{ $user->created_at?->format('M Y') }}
                        </span>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <a href="{{ route('contact') }}" class="px-4 py-2 rounded-xl border border-border hover:border-foreground transition text-sm font-semibold">
                        Message
                    </a>
                </div>
            </div>

            <div class="space-y-6">
                <h2 class="text-2xl font-bold font-display">Uploads</h2>
                @if($products->isEmpty())
                    <div class="glass-card rounded-3xl p-6 text-muted-foreground">
                        No uploads yet.
                    </div>
                @else
                    <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach($products as $product)
                            @php
                                $discount = $product->discount_percentage ?? ($product->original_price > 0 ? round((1 - $product->price / $product->original_price) * 100) : 0);
                            @endphp
                            <a href="{{ route('bundles.show', $product->slug) }}" class="glass-card rounded-3xl overflow-hidden hover-lift group">
                                <div class="relative overflow-hidden">
                                    @if($product->image_url)
                                        <img src="{{ $product->image_url }}" alt="{{ $product->title }}" class="w-full h-48 object-cover transition-transform duration-700 group-hover:scale-110">
                                    @else
                                        <div class="w-full h-48 bg-gradient-to-br from-cyan-500/20 to-violet-500/20 flex items-center justify-center">
                                            <i data-lucide="package" class="w-10 h-10 text-cyan-400/60"></i>
                                        </div>
                                    @endif
                                    <div class="absolute inset-0 bg-gradient-to-t from-background/90 via-background/30 to-transparent"></div>
                                    <div class="absolute top-3 left-3 flex gap-2">
                                        @if($product->is_bundle)
                                            <span class="bg-violet-500/20 text-violet-400 text-xs font-bold px-3 py-1 rounded-full flex items-center gap-1">
                                                <i data-lucide="package" class="w-3 h-3"></i>
                                                Bundle
                                            </span>
                                        @endif
                                    </div>
                                    <div class="absolute bottom-3 left-3 right-3">
                                        <h3 class="text-lg font-bold font-display line-clamp-1">{{ $product->title }}</h3>
                                        <p class="text-xs text-muted-foreground line-clamp-2">{{ $product->description }}</p>
                                    </div>
                                </div>
                                <div class="p-4 space-y-3">
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <span class="text-2xl font-bold gradient-text">${{ number_format($product->price, 2) }}</span>
                                            @if($product->original_price)
                                                <span class="text-muted-foreground line-through ml-2 text-sm">${{ number_format($product->original_price, 2) }}</span>
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
                                </div>
                            </a>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </section>
@endsection

