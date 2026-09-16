<?php

namespace App\Http\Controllers;

use App\Models\Coupon;
use App\Models\Product;
use App\Models\Setting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class CartController extends Controller
{
    public function show(Request $request): View
    {
        $cart = $request->session()->get('cart', []);
        $couponCode = $request->session()->get('coupon_code');
        $coupon = null;
        $discount = 0;
        
        if ($couponCode) {
            $coupon = Coupon::where('code', $couponCode)->first();
            if ($coupon) {
                $subtotal = collect($cart)->sum(fn ($item) => ($item['price'] ?? 0) * ($item['qty'] ?? 1));
                $validation = $coupon->isValid($request->user()?->id, $subtotal);
                if (!$validation['valid']) {
                    // Invalid coupon, remove it
                    $request->session()->forget('coupon_code');
                    $coupon = null;
                } else {
                    $discount = $coupon->calculateDiscount($subtotal);
                }
            }
        }

        // Calculate coin discount
        $coinDiscount = 0;
        $coinsToUse = $request->session()->get('coins_to_use', 0);
        if ($coinsToUse > 0) {
            $pointsPerDollar = (int) Setting::get('points_per_dollar', 1000);
            $coinDiscount = min($coinsToUse / $pointsPerDollar, collect($cart)->sum(fn ($item) => ($item['price'] ?? 0) * ($item['qty'] ?? 1)) - $discount);
        }

        $user = $request->user();
        $userPoints = $user ? $user->getPoints() : 0;
        $pointsPerDollar = (int) Setting::get('points_per_dollar', 1000);

        // Upsells: products from the same categories as cart items, not already in cart.
        $upsells = collect();
        if (! empty($cart)) {
            $cartIds = collect($cart)->pluck('id')->all();
            $categoryIds = Product::whereIn('id', $cartIds)->pluck('category_id')->filter()->unique();
            $upsells = Product::where('is_active', true)
                ->whereNotIn('id', $cartIds)
                ->when($categoryIds->isNotEmpty(), fn ($q) => $q->whereIn('category_id', $categoryIds))
                ->orderBy('sort_order')
                ->limit(4)
                ->get();
        }

        return view('cart.index', [
            'title' => 'Your Cart - SketchUp Collection',
            'metaDescription' => 'Review your items before checkout.',
            'cart' => $cart,
            'coupon' => $coupon,
            'discount' => $discount,
            'coinDiscount' => $coinDiscount,
            'coinsToUse' => $coinsToUse,
            'userPoints' => $userPoints,
            'pointsPerDollar' => $pointsPerDollar,
            'upsells' => $upsells,
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

    public function applyCoupon(Request $request): JsonResponse|RedirectResponse
    {
        $request->validate([
            'code' => 'required|string|max:50',
        ]);

        $coupon = Coupon::where('code', strtoupper($request->code))->first();
        
        if (!$coupon) {
            $message = 'Invalid coupon code.';
            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'message' => $message], 422);
            }
            return back()->withErrors(['coupon' => $message]);
        }

        $cart = $request->session()->get('cart', []);
        $subtotal = collect($cart)->sum(fn ($item) => ($item['price'] ?? 0) * ($item['qty'] ?? 1));
        
        $validation = $coupon->isValid($request->user()?->id, $subtotal);
        
        if (!$validation['valid']) {
            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'message' => $validation['message']], 422);
            }
            return back()->withErrors(['coupon' => $validation['message']]);
        }

        // Apply coupon
        $request->session()->put('coupon_code', $coupon->code);
        $discount = $coupon->calculateDiscount($subtotal);
        $total = $subtotal - $discount;

        $message = 'Coupon applied successfully!';
        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => $message,
                'coupon' => [
                    'code' => $coupon->code,
                    'type' => $coupon->type,
                    'value' => $coupon->value,
                ],
                'discount' => $discount,
                'total' => $total,
            ]);
        }

        return back()->with('status', $message);
    }

    public function removeCoupon(Request $request): JsonResponse|RedirectResponse
    {
        $request->session()->forget('coupon_code');

        $message = 'Coupon removed successfully.';
        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'message' => $message]);
        }

        return back()->with('status', $message);
    }

    public function remove($productId, Request $request): JsonResponse|RedirectResponse
    {
        $cart = $request->session()->get('cart', []);
        
        if (isset($cart[$productId])) {
            unset($cart[$productId]);
            $request->session()->put('cart', $cart);
            
            // If cart is empty, also remove coupon
            if (empty($cart)) {
                $request->session()->forget('coupon_code');
            }
            
            $message = 'Item removed from cart.';
            if ($request->wantsJson()) {
                $counts = $this->cartCounts($cart);
                return response()->json([
                    'success' => true,
                    'message' => $message,
                    'items' => $counts['items'],
                    'quantity' => $counts['quantity'],
                ]);
            }
            
            return back()->with('status', $message);
        }
        
        $message = 'Item not found in cart.';
        if ($request->wantsJson()) {
            return response()->json(['success' => false, 'message' => $message], 404);
        }
        
        return back()->withErrors(['cart' => $message]);
    }

    public function clear(Request $request): JsonResponse|RedirectResponse
    {
        $request->session()->forget('cart');
        $request->session()->forget('coupon_code');
        $request->session()->forget('coins_to_use');
        
        $message = 'Cart cleared successfully.';
        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => $message,
                'items' => 0,
                'quantity' => 0,
            ]);
        }
        
        return redirect()->route('cart.show')->with('status', $message);
    }

    public function applyCoins(Request $request): JsonResponse|RedirectResponse
    {
        $user = $request->user();
        
        if (!$user) {
            $message = 'You must be logged in to use coins.';
            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'message' => $message], 401);
            }
            return back()->withErrors(['coins' => $message]);
        }

        $request->validate([
            'coins' => 'required|integer|min:1',
        ]);

        $coinsToUse = (int) $request->coins;
        $userPoints = $user->getPoints();

        if ($coinsToUse > $userPoints) {
            $message = "You don't have enough coins. You have {$userPoints} SKP coins.";
            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'message' => $message], 422);
            }
            return back()->withErrors(['coins' => $message]);
        }

        $cart = $request->session()->get('cart', []);
        $subtotal = collect($cart)->sum(fn ($item) => ($item['price'] ?? 0) * ($item['qty'] ?? 1));
        
        // Calculate coupon discount if exists
        $couponDiscount = 0;
        $couponCode = $request->session()->get('coupon_code');
        if ($couponCode) {
            $coupon = Coupon::where('code', $couponCode)->first();
            if ($coupon) {
                $validation = $coupon->isValid($user->id, $subtotal);
                if ($validation['valid']) {
                    $couponDiscount = $coupon->calculateDiscount($subtotal);
                }
            }
        }

        // Calculate maximum coins that can be used (after coupon discount)
        $pointsPerDollar = (int) Setting::get('points_per_dollar', 1000);
        $maxCoinValue = ($subtotal - $couponDiscount) * $pointsPerDollar;
        $maxCoinsToUse = min($coinsToUse, $maxCoinValue, $userPoints);

        if ($maxCoinsToUse < $coinsToUse) {
            $message = "You can only use up to {$maxCoinsToUse} coins for this order.";
            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'message' => $message], 422);
            }
            return back()->withErrors(['coins' => $message]);
        }

        $request->session()->put('coins_to_use', $maxCoinsToUse);
        
        $coinDiscount = $maxCoinsToUse / $pointsPerDollar;
        $total = $subtotal - $couponDiscount - $coinDiscount;

        $message = 'Coins applied successfully!';
        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => $message,
                'coins_used' => $maxCoinsToUse,
                'coin_discount' => $coinDiscount,
                'total' => $total,
            ]);
        }

        return back()->with('status', $message);
    }

    public function removeCoins(Request $request): JsonResponse|RedirectResponse
    {
        $request->session()->forget('coins_to_use');

        $message = 'Coins removed successfully.';
        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'message' => $message]);
        }

        return back()->with('status', $message);
    }

    private function cartCounts(array $cart): array
    {
        $items = count($cart);
        $quantity = collect($cart)->sum(fn ($item) => $item['qty'] ?? 0);

        return ['items' => $items, 'quantity' => $quantity];
    }
}

