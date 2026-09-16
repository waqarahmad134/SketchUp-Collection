@extends('layouts.app')

@section('content')
    <section class="py-16">
        <div class="container mx-auto px-4">
            <div class="max-w-6xl mx-auto">
                <h1 class="text-3xl md:text-4xl font-bold font-display mb-2">My <span class="gradient-text">Wishlist</span></h1>
                <p class="text-muted-foreground mb-8">{{ $products->count() }} saved item(s)</p>

                @if($products->count() > 0)
                    <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-6">
                        @foreach($products as $product)
                            <div class="glass-card rounded-3xl overflow-hidden group">
                                <div class="relative overflow-hidden">
                                    @if($product->image_url)
                                        <img src="{{ $product->image_url }}" alt="{{ $product->title }}" loading="lazy" class="w-full h-48 object-cover transition-transform duration-700 group-hover:scale-110">
                                    @else
                                        <div class="w-full h-48 bg-gradient-to-br from-cyan-500/20 to-violet-500/20 flex items-center justify-center">
                                            <i data-lucide="package" class="w-12 h-12 text-cyan-400/50"></i>
                                        </div>
                                    @endif
                                    <form method="POST" action="{{ route('wishlist.toggle', $product) }}" class="absolute top-3 right-3">
                                        @csrf
                                        <button type="submit" aria-label="Remove from wishlist" class="w-10 h-10 rounded-full bg-background/80 backdrop-blur flex items-center justify-center text-red-400 hover:scale-110 transition">
                                            <i data-lucide="heart" class="w-5 h-5 fill-current"></i>
                                        </button>
                                    </form>
                                </div>
                                <div class="p-5">
                                    <h3 class="font-semibold mb-2 line-clamp-2">{{ $product->title }}</h3>
                                    <div class="flex items-center justify-between">
                                        <span class="text-xl font-bold gradient-text">{{ \App\Support\Currency::format($product->price) }}</span>
                                        <a href="{{ route('bundles.show', $product->slug) }}" class="inline-flex items-center gap-1 text-sm text-cyan-400 font-medium">
                                            View <i data-lucide="arrow-right" class="w-4 h-4"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="glass-card rounded-3xl p-12 text-center">
                        <i data-lucide="heart" class="w-16 h-16 text-muted-foreground mx-auto mb-4"></i>
                        <h2 class="text-2xl font-bold mb-2">Your wishlist is empty</h2>
                        <p class="text-muted-foreground mb-6">Tap the heart on any bundle to save it here.</p>
                        <a href="{{ route('bundles.index') }}" class="inline-flex items-center gap-2 px-8 py-4 rounded-xl bg-gradient-to-r from-cyan-500 to-violet-500 text-background font-semibold shadow-lg hover:shadow-xl transition">
                            Browse Bundles <i data-lucide="arrow-right" class="w-5 h-5"></i>
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </section>
@endsection
