@extends('layouts.app')

@section('content')
    @php
        $discount = $product->discount_percentage ?? ($product->original_price > 0 ? round((1 - $product->price / $product->original_price) * 100) : 0);
        $images = collect($product->images ?? []);
        
        // Build full image URLs array (main image + gallery images)
        $allImages = collect();
        if ($product->image_url) {
            $allImages->push($product->image_url);
        }
        foreach ($images as $img) {
            if (is_string($img)) {
                $imgUrl = \Illuminate\Support\Str::startsWith($img, ['http://', 'https://']) 
                    ? $img 
                    : \Illuminate\Support\Facades\Storage::disk('public')->url($img);
                $allImages->push($imgUrl);
            }
        }
    @endphp

    <div class="container mx-auto px-4 py-6">
        <a href="{{ route('bundles.index') }}" class="inline-flex items-center gap-2 text-muted-foreground hover:text-cyan-400 transition-colors">
            <i data-lucide="arrow-left" class="w-4 h-4"></i>
            Back to Products
        </a>
    </div>

    <section class="pt-6 pb-12 mesh-gradient relative overflow-hidden">
        <div class="absolute top-0 left-1/2 -translate-x-1/2 w-[800px] h-[400px] bg-gradient-to-b from-cyan-500/10 to-transparent rounded-full blur-3xl"></div>
        
        <div class="container mx-auto px-4 relative z-10">
            <div class="grid lg:grid-cols-2 gap-12">
                <div class="space-y-4">
                    @if($allImages->count() > 0)
                    {{-- Main Image Slider with Swiper --}}
<div class="glass-card rounded-3xl overflow-hidden relative group">
    <div class="swiper product-main-slider">
        <div class="swiper-wrapper">
            @foreach($allImages as $index => $imgUrl)
                <div class="swiper-slide">
                    <a href="{{ $imgUrl }}" class="glightbox" data-gallery="product-gallery">
                        <img src="{{ $imgUrl }}" alt="{{ $product->title }} - Image {{ $index + 1 }}" class="w-full h-auto object-cover cursor-zoom-in">
                    </a>
                </div>
            @endforeach
        </div>
        
        @if($allImages->count() > 1)
            <div class="swiper-button-prev !w-12 !h-12 !rounded-full !bg-background/80 !backdrop-blur-sm !border !border-border hover:!bg-background !opacity-0 group-hover:!opacity-100 !transition-opacity"></div>
            <div class="swiper-button-next !w-12 !h-12 !rounded-full !bg-background/80 !backdrop-blur-sm !border !border-border hover:!bg-background !opacity-0 group-hover:!opacity-100 !transition-opacity"></div>
            <div class="swiper-pagination !bottom-4"></div>
        @endif
        
        <button onclick="openGallery()" class="absolute top-4 right-4 w-12 h-12 rounded-full bg-background/80 backdrop-blur-sm border border-border hover:bg-background flex items-center justify-center transition-opacity opacity-0 group-hover:opacity-100 z-20" title="View Gallery">
            <i data-lucide="maximize" class="w-5 h-5"></i>
        </button>
    </div>
    
    @if($allImages->count() > 1)
        <div class="swiper product-thumb-slider mt-4">
            <div class="swiper-wrapper">
                @foreach($allImages as $index => $imgUrl)
                    <div class="swiper-slide !w-auto !cursor-pointer">
                        <div class="glass-card rounded-2xl overflow-hidden border-2 border-transparent hover:border-cyan-500/50 transition-colors thumbnail-slide" data-index="{{ $index }}">
                            <img src="{{ $imgUrl }}" alt="Thumbnail {{ $index + 1 }}" class="w-24 h-24 object-cover">
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</div>
                    @else
                        <div class="glass-card rounded-3xl overflow-hidden">
                            <div class="w-full h-96 bg-gradient-to-br from-cyan-500/20 to-violet-500/20 flex items-center justify-center">
                                <i data-lucide="package" class="w-16 h-16 text-cyan-400/50"></i>
                            </div>
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
                            <div class="text-xl text-muted-foreground mb-6 prose prose-invert max-w-none">
                                @if($product->full_description)
                                    {!! $product->full_description !!}
                                @else
                                    {{ strip_tags($product->description) }}
                                @endif
                            </div>
                        @endif
                        
                        @if($product->tags && $product->tags->count() > 0)
                            <div class="flex flex-wrap gap-2 mb-6">
                                @foreach($product->tags as $tag)
                                    <a href="{{ route('bundles.index', ['tag' => $tag->slug]) }}" 
                                       class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-sm font-medium transition-all hover:scale-105 {{ $tag->color ? '' : 'bg-cyan-500/20 text-cyan-400 border border-cyan-500/40' }}"
                                       @if($tag->color)
                                           style="background-color: {{ $tag->color }}20; color: {{ $tag->color }}; border: 1px solid {{ $tag->color }}40;"
                                       @endif
                                    >
                                        @if($tag->color)
                                            <span class="w-2 h-2 rounded-full" style="background-color: {{ $tag->color }}"></span>
                                        @endif
                                        {{ $tag->name }}
                                    </a>
                                @endforeach
                            </div>
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
                        @if($product->is_digital && (float)$product->price <= 0 && $product->download_file_url)
                            <div class="space-y-3">
                                <a href="{{ $product->download_file_url }}" target="_blank" class="w-full px-4 py-3 rounded-xl bg-gradient-to-r from-cyan-500 to-violet-500 text-background font-semibold shadow-lg hover:shadow-xl transition inline-flex items-center justify-center gap-2">
                                    <i data-lucide="download" class="w-5 h-5"></i>
                                    Instant Download
                                </a>
                            </div>
                        @else
                            <div class="space-y-3">
                                <form method="POST" action="{{ route('cart.add', $product) }}" data-cart-add>
                                    @csrf
                                    <button type="submit" class="w-full px-4 py-3 rounded-xl bg-gradient-to-r from-cyan-500 to-violet-500 text-background font-semibold shadow-lg hover:shadow-xl transition inline-flex items-center justify-center gap-2">
                                        <i data-lucide="shopping-cart" class="w-5 h-5"></i>
                                        Add to Cart
                                    </button>
                                </form>
                                <form method="POST" action="{{ route('cart.buyNow', $product) }}">
                                    @csrf
                                    <button type="submit" class="w-full px-4 py-3 rounded-xl border border-border text-foreground font-semibold hover:border-foreground transition inline-flex items-center justify-center gap-2">
                                        <i data-lucide="download" class="w-5 h-5"></i>
                                        Buy Now
                                    </button>
                                </form>
                                @include('partials.wishlist-button', ['product' => $product])
                            </div>
                        @endif
                    </div>

                    @if($product->is_digital && (float)$product->price <= 0 && ($product->download_links || $product->download_file_url))
                        <div class="glass-card rounded-3xl p-6 space-y-4">
                            <h3 class="font-semibold flex items-center gap-2">
                                <i data-lucide="cloud-download" class="w-5 h-5"></i>
                                Digital Delivery
                            </h3>
                            @if($product->download_file_url)
                                <a href="{{ $product->download_file_url }}" class="flex items-center justify-between px-4 py-3 rounded-xl border border-border hover:border-foreground transition" target="_blank">
                                    <span class="font-medium">Download File</span>
                                    <i data-lucide="arrow-up-right" class="w-4 h-4"></i>
                                </a>
                            @endif
                            @if($product->download_links)
                                <div class="space-y-2">
                                    @foreach($product->download_links as $link)
                                        <a href="{{ $link['url'] ?? '#' }}" target="_blank" class="flex items-center justify-between px-4 py-3 rounded-xl bg-card hover:border hover:border-foreground transition">
                                            <span class="font-medium">{{ $link['title'] ?? 'Download Link' }}</span>
                                            <i data-lucide="external-link" class="w-4 h-4"></i>
                                        </a>
                                    @endforeach
                                </div>
                            @endif
                            <p class="text-xs text-muted-foreground">Links can include Google Drive, Mega, Dropbox, or direct files.</p>
                        </div>
                    @endif

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
                                    <span class="font-medium capitalize">{{ $product->category->name }}</span>
                                </div>
                            @endif
                            <div class="flex items-center justify-between">
                                <span class="text-muted-foreground">License</span>
                                <span class="font-medium">Commercial</span>
                            </div>
                        </div>
                    </div>

                    @php
                        $creator = $product->user;
                        $authorName = $creator->name ?? 'SketchUp Collection Team';
                        $authorRole = 'Uploader';
                        $authorAvatar = $creator?->avatar ?? 'https://ui-avatars.com/api/?name=' . urlencode($authorName) . '&background=22b5ff&color=fff';
@endphp
                    <div class="glass-card rounded-3xl p-6 flex items-center gap-4">
                        @if($creator)
                            <a href="{{ route('creators.show', $creator) }}" class="flex-shrink-0 block">
                                <img src="{{ $authorAvatar }}" alt="{{ $authorName }}" class="w-14 h-14 rounded-full border border-border hover:scale-105 transition-transform">
                            </a>
                        @else
                            <div class="flex-shrink-0">
                                <img src="{{ $authorAvatar }}" alt="{{ $authorName }}" class="w-14 h-14 rounded-full border border-border">
                            </div>
                        @endif
                        <div class="flex-1">
                            <h4 class="text-lg font-semibold">{{ $authorName }}</h4>
                            <p class="text-sm text-muted-foreground">{{ $authorRole }}</p>
                            <div class="flex items-center gap-4 mt-2">
                                @if($creator)
                                    <a href="{{ route('creators.show', $creator) }}" class="text-cyan-400 hover:text-cyan-300 text-sm font-medium">View profile & uploads</a>
                                @endif
                                <a href="{{ route('contact') }}" class="text-sm text-muted-foreground hover:text-foreground">Report this product</a>
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

    @if($product->is_bundle && $product->included_products_models->isNotEmpty())
        <section class="py-16 bg-card">
            <div class="container mx-auto px-4">
                <div class="max-w-4xl mx-auto">
                    <h2 class="text-3xl font-bold font-display mb-8">
                        This Bundle Includes
                    </h2>
                    <p class="text-muted-foreground mb-6">
                        This bundle combines {{ $product->included_products_models->count() }} products for maximum value:
                        @if($product->bundle_savings_percentage > 0)
                            <span class="text-cyan-400 font-semibold">Save {{ $product->bundle_savings_percentage }}%</span>
                        @endif
                    </p>
                    <div class="grid md:grid-cols-2 gap-4">
                        @foreach($product->included_products_models as $included)
                            <div class="glass-card rounded-2xl p-4 flex items-center gap-4">
                                <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-cyan-500/20 to-violet-500/20 flex items-center justify-center flex-shrink-0">
                                    <i data-lucide="file-box" class="w-6 h-6 text-cyan-400"></i>
                                </div>
                                <div class="flex-1">
                                    <h4 class="font-semibold">{{ $included->title }}</h4>
                                    <p class="text-sm text-muted-foreground">
                                        {{ $included->file_count }} files • {{ $included->file_size }}
                                    </p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </section>
    @endif

    <section class="py-16 bg-background">
        <div class="container mx-auto px-4">
            <div class="max-w-4xl mx-auto space-y-8">
                <div class="flex items-center justify-between gap-4">
                    <h2 class="text-3xl font-bold font-display">Reviews</h2>
                    @php
                        $avg = $product->reviews->avg('rating');
                        $count = $product->reviews->count();
                    @endphp
                    @if($count > 0)
                        <div class="flex items-center gap-3 text-sm text-muted-foreground">
                            <i data-lucide="star" class="w-4 h-4 text-cyan-400" style="fill: currentColor;"></i>
                            <span>{{ number_format($avg, 1) }} / 5</span>
                            <span>·</span>
                            <span>{{ $count }} rating{{ $count > 1 ? 's' : '' }}</span>
                        </div>
                    @endif
                </div>

                <div class="grid md:grid-cols-2 gap-4">
                    @forelse($product->reviews as $review)
                        <div class="glass-card rounded-3xl p-5 space-y-3">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-full bg-gradient-to-br from-cyan-500 to-violet-500 text-background flex items-center justify-center font-semibold">
                                        {{ mb_substr($review->name, 0, 1) }}
                                    </div>
                                    <div>
                                        <p class="font-semibold">{{ $review->name }}</p>
                                        <p class="text-xs text-muted-foreground">{{ $review->created_at?->format('M Y') }}</p>
                                    </div>
                                </div>
                                <div class="flex items-center gap-1">
                                    @for($i = 0; $i < 5; $i++)
                                        <i data-lucide="star" class="w-4 h-4 {{ $i < $review->rating ? 'text-cyan-400 fill-current' : 'text-muted-foreground' }}"></i>
                                    @endfor
                                </div>
                            </div>
                            @if($review->comment)
                                <p class="text-sm text-foreground">{{ $review->comment }}</p>
                            @endif
                        </div>
                    @empty
                        <div class="md:col-span-2 glass-card rounded-3xl p-6 text-muted-foreground text-sm">
                            No reviews yet. Be the first to review this {{ $product->is_bundle ? 'bundle' : 'product' }}.
                        </div>
                    @endforelse
                </div>

                <div class="glass-card rounded-3xl p-6 space-y-4">
                    <h3 class="text-xl font-bold font-display">Leave a review</h3>
                    @if(session('status'))
                        <div class="rounded-xl border border-cyan-500/40 bg-cyan-500/10 text-cyan-100 p-3 text-sm">
                            {{ session('status') }}
                        </div>
                    @endif
                    <p class="text-sm text-muted-foreground">Share your experience with this {{ $product->is_bundle ? 'bundle' : 'product' }}.</p>
                    <form class="space-y-3" method="POST" action="{{ route('reviews.store', $product->slug) }}">
                        @csrf
                        <div class="grid md:grid-cols-2 gap-3">
                            <input type="text" name="name" placeholder="Your name" value="{{ old('name') }}" class="w-full px-4 py-3 rounded-xl bg-card border border-border focus:border-cyan-500 focus:ring-2 focus:ring-cyan-500/30 outline-none" required>
                            <input type="email" name="email" placeholder="Your email" value="{{ old('email') }}" class="w-full px-4 py-3 rounded-xl bg-card border border-border focus:border-cyan-500 focus:ring-2 focus:ring-cyan-500/30 outline-none">
                        </div>
                        <div class="grid md:grid-cols-2 gap-3">
                            <select name="rating" class="w-full px-4 py-3 rounded-xl bg-card border border-border focus:border-cyan-500 focus:ring-2 focus:ring-cyan-500/30 outline-none" required>
                                <option value="" disabled {{ old('rating') ? '' : 'selected' }}>Rating</option>
                                @for($i=5; $i>=1; $i--)
                                    <option value="{{ $i }}" {{ old('rating') == $i ? 'selected' : '' }}>{{ $i }} star{{ $i > 1 ? 's' : '' }}</option>
                                @endfor
                            </select>
                        </div>
                        <textarea name="comment" rows="4" placeholder="Your review..." class="w-full px-4 py-3 rounded-xl bg-card border border-border focus:border-cyan-500 focus:ring-2 focus:ring-cyan-500/30 outline-none">{{ old('comment') }}</textarea>
                        <button type="submit" class="px-5 py-3 rounded-xl bg-gradient-to-r from-cyan-500 to-violet-500 text-background font-semibold shadow-lg hover:shadow-xl transition inline-flex items-center gap-2">
                            <i data-lucide="message-square" class="w-5 h-5"></i>
                            Submit review
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </section>

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
    @if($relatedProducts->count() > 0)
    <section class="py-16 bg-background">
        <div class="container mx-auto px-4">
            <div class="max-w-6xl mx-auto">
                <h2 class="text-3xl font-bold font-display mb-2 text-center">
                    Related <span class="gradient-text">Bundles</span>
                </h2>
                <p class="text-muted-foreground text-center mb-10">More SketchUp collections you may like</p>
                <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-6">
                    @foreach($relatedProducts as $related)
                        <a href="{{ route('bundles.show', $related->slug) }}" class="glass-card rounded-3xl overflow-hidden group hover:border-cyan-500/50 transition-all duration-300">
                            @if($related->image_url)
                                <div class="h-40 overflow-hidden">
                                    <img src="{{ $related->image_url }}" alt="{{ $related->title }}" loading="lazy" class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110">
                                </div>
                            @endif
                            <div class="p-5">
                                <h3 class="font-semibold mb-2 line-clamp-2 group-hover:text-cyan-400 transition-colors">{{ $related->title }}</h3>
                                <div class="flex items-center justify-between">
                                    <span class="text-xl font-bold gradient-text">${{ number_format($related->price, 2) }}</span>
                                    <span class="inline-flex items-center gap-1 text-sm text-cyan-400 font-medium">
                                        View <i data-lucide="arrow-right" class="w-4 h-4"></i>
                                    </span>
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        </div>
    </section>
    @endif

    @if(isset($recentlyViewed) && $recentlyViewed->count() > 0)
    <section class="py-16">
        <div class="container mx-auto px-4">
            <div class="max-w-6xl mx-auto">
                <h2 class="text-3xl font-bold font-display mb-2 text-center">
                    Recently <span class="gradient-text">Viewed</span>
                </h2>
                <p class="text-muted-foreground text-center mb-10">Pick up where you left off</p>
                <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-6">
                    @foreach($recentlyViewed as $recent)
                        <a href="{{ route('bundles.show', $recent->slug) }}" class="glass-card rounded-3xl overflow-hidden group hover:border-cyan-500/50 transition-all duration-300">
                            @if($recent->image_url)
                                <div class="h-40 overflow-hidden">
                                    <img src="{{ $recent->image_url }}" alt="{{ $recent->title }}" loading="lazy" class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110">
                                </div>
                            @endif
                            <div class="p-5">
                                <h3 class="font-semibold mb-2 line-clamp-2 group-hover:text-cyan-400 transition-colors">{{ $recent->title }}</h3>
                                <div class="flex items-center justify-between">
                                    <span class="text-xl font-bold gradient-text">${{ number_format($recent->price, 2) }}</span>
                                    <span class="inline-flex items-center gap-1 text-sm text-cyan-400 font-medium">
                                        View <i data-lucide="arrow-right" class="w-4 h-4"></i>
                                    </span>
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        </div>
    </section>
    @endif
    
@endsection

@push('head')
    {{-- Swiper.js CSS --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css">
    {{-- GLightbox CSS --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/glightbox/dist/css/glightbox.min.css">
@endpush

@push('scripts')
    {{-- Swiper.js JS --}}
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
    {{-- GLightbox JS --}}
    <script src="https://cdn.jsdelivr.net/npm/glightbox/dist/js/glightbox.min.js"></script>
    
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // Initialize Lucide icons
            if (window.lucide?.createIcons) {
                window.lucide.createIcons();
            }
            
            const imageCount = {{ $allImages->count() }};
            let mainSwiper = null;
            let lightbox = null;
            
            // Initialize Swiper for main slider
            @if($allImages->count() > 1)
            // Initialize thumbnail slider first
            const thumbSwiper = new Swiper('.product-thumb-slider', {
                spaceBetween: 12,
                slidesPerView: 4,
                freeMode: true,
                watchSlidesProgress: true,
                breakpoints: {
                    640: {
                        slidesPerView: 5,
                    },
                    1024: {
                        slidesPerView: 6,
                    },
                },
            });
            
            // Initialize main slider with thumbs
            mainSwiper = new Swiper('.product-main-slider', {
                spaceBetween: 0,
                loop: false,
                navigation: {
                    nextEl: '.swiper-button-next',
                    prevEl: '.swiper-button-prev',
                },
                pagination: {
                    el: '.swiper-pagination',
                    clickable: true,
                },
                thumbs: {
                    swiper: thumbSwiper,
                },
            });
            
            // Link thumbnails to main slider (click to navigate)
            document.querySelectorAll('.thumbnail-slide').forEach((thumb, index) => {
                thumb.addEventListener('click', () => {
                    mainSwiper.slideTo(index);
                });
            });
            
            // Update active thumbnail on slide change
            mainSwiper.on('slideChange', () => {
                document.querySelectorAll('.thumbnail-slide').forEach((thumb, index) => {
                    if (index === mainSwiper.activeIndex) {
                        thumb.classList.add('!border-cyan-500');
                        thumb.classList.remove('border-transparent');
                    } else {
                        thumb.classList.remove('!border-cyan-500');
                        thumb.classList.add('border-transparent');
                    }
                });
            });
            
            // Set initial active thumbnail
            const firstThumb = document.querySelector('.thumbnail-slide');
            if (firstThumb) {
                firstThumb.classList.add('!border-cyan-500');
                firstThumb.classList.remove('border-transparent');
            }
            @endif
            
            // Initialize GLightbox for gallery popup
            lightbox = GLightbox({
                selector: '.glightbox',
                touchNavigation: true,
                loop: true,
                autoplayVideos: false,
                openEffect: 'fade',
                closeEffect: 'fade',
            });
            
            // Function to open gallery (called by magnifier button)
            window.openGallery = function() {
                @if($allImages->count() > 0)
                    const currentIndex = {{ $allImages->count() > 1 ? '(mainSwiper ? mainSwiper.activeIndex : 0)' : '0' }};
                    lightbox.openAt(currentIndex);
                @endif
            };
        });
    </script>
    
    <style>
        /* Custom Swiper Styles */
        .swiper-button-next,
        .swiper-button-prev {
            color: hsl(var(--foreground));
        }
        
        .swiper-button-next:after,
        .swiper-button-prev:after {
            font-size: 18px;
        }
        
        .swiper-pagination-bullet {
            background: hsl(var(--muted-foreground) / 0.5);
            opacity: 1;
        }
        
        .swiper-pagination-bullet-active {
            background: hsl(187 100% 50%);
        }
        
        /* GLightbox Custom Styles */
        .glightbox-clean .gslide-description {
            background: hsl(var(--card));
            color: hsl(var(--foreground));
        }
    </style>
@endpush
