@php
    $inWishlist = in_array($product->id, session('wishlist', []));
@endphp
<form method="POST" action="{{ route('wishlist.toggle', $product) }}" class="inline">
    @csrf
    <button type="submit" aria-label="{{ $inWishlist ? 'Remove from wishlist' : 'Add to wishlist' }}"
        class="inline-flex items-center gap-2 px-5 py-3 rounded-xl border font-semibold text-sm transition
            {{ $inWishlist ? 'border-red-500/50 text-red-400 bg-red-500/10' : 'border-border hover:border-red-500/50 hover:text-red-400' }}">
        <i data-lucide="heart" class="w-4 h-4 {{ $inWishlist ? 'fill-current' : '' }}"></i>
        {{ $inWishlist ? 'Saved' : 'Wishlist' }}
    </button>
</form>
