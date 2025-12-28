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

    <section class="pt-6 pb-12 mesh-gradient relative overflow-hidden">
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
@endsection

