<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class CartController extends Controller
{
    public function show(Request $request): View
    {
        $cart = $request->session()->get('cart', []);

        return view('cart.index', [
            'title' => 'Your Cart - SketchUp Collection',
            'metaDescription' => 'Review your items before checkout.',
            'cart' => $cart,
        ]);
    }

    public function add(Product $product, Request $request)
    {
        $cart = $request->session()->get('cart', []);
        $already = isset($cart[$product->id]);

        $cart[$product->id] = [
            'id' => $product->id,
            'slug' => $product->slug,
            'title' => $product->title,
            'price' => $product->price,
            'qty' => $already ? ($cart[$product->id]['qty'] ?? 1) : 1,
            'image' => $product->image_url,
        ];
        $request->session()->put('cart', $cart);

        $counts = $this->cartCounts($cart);
        $message = $already ? 'Already in cart' : 'Added to cart';

        if ($request->wantsJson()) {
            return response()->json([
                'message' => $message,
                'items' => $counts['items'],
                'quantity' => $counts['quantity'],
            ]);
        }

        return back()->with('status', $message);
    }

    public function buyNow(Product $product, Request $request): RedirectResponse
    {
        // Replace cart with single item for immediate checkout
        $request->session()->put('cart', [
            $product->id => [
                'id' => $product->id,
                'slug' => $product->slug,
                'title' => $product->title,
                'price' => $product->price,
                'qty' => 1,
                'image' => $product->image_url,
            ],
        ]);

        if (!Auth::check()) {
            return redirect()->route('login')->with('status', 'Please login to checkout.');
        }

        // Go straight to Stripe checkout
        return redirect()->route('checkout.stripe.start');
    }

    private function cartCounts(array $cart): array
    {
        $items = count($cart);
        $quantity = collect($cart)->sum(fn ($item) => $item['qty'] ?? 0);

        return ['items' => $items, 'quantity' => $quantity];
    }
}

