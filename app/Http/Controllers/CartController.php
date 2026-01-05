<?php

namespace App\Http\Controllers;

use App\Models\Coupon;
use App\Models\Product;
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

        return view('cart.index', [
            'title' => 'Your Cart - SketchUp Collection',
            'metaDescription' => 'Review your items before checkout.',
            'cart' => $cart,
            'coupon' => $coupon,
            'discount' => $discount,
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

    private function cartCounts(array $cart): array
    {
        $items = count($cart);
        $quantity = collect($cart)->sum(fn ($item) => $item['qty'] ?? 0);

        return ['items' => $items, 'quantity' => $quantity];
    }
}

