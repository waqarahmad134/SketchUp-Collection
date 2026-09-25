<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class WishlistController extends Controller
{
    /**
     * Toggle a product in the session wishlist.
     */
    public function toggle(Request $request, Product $product): RedirectResponse|\Illuminate\Http\JsonResponse
    {
        $wishlist = $request->session()->get('wishlist', []);

        if (in_array($product->id, $wishlist)) {
            $wishlist = array_values(array_diff($wishlist, [$product->id]));
            $message = 'Removed from your wishlist.';
        } else {
            $wishlist[] = $product->id;
            $message = 'Added to your wishlist.';
        }

        $request->session()->put('wishlist', $wishlist);

        if ($request->expectsJson()) {
            return response()->json([
                'in_wishlist' => in_array($product->id, $wishlist),
                'count' => count($wishlist),
            ]);
        }

        return back()->with('status', $message);
    }

    /**
     * Wishlist page.
     */
    public function index(Request $request): View
    {
        $ids = $request->session()->get('wishlist', []);

        $products = Product::whereIn('id', $ids)
            ->where('is_active', true)
            ->get()
            ->sortBy(fn ($p) => array_search($p->id, $ids));

        return view('wishlist.index', [
            'title' => 'My Wishlist - SketchUp Collection',
            'metaDescription' => 'Your saved bundles and products.',
            'products' => $products,
        ]);
    }
}
