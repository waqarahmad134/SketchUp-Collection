<?php

namespace App\Http\Controllers;

use App\Models\Coupon;
use App\Models\CouponUsage;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Referral;
use App\Models\Setting;
use App\Models\Transaction;
use App\Models\PointsTransaction;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Stripe\StripeClient;

class CheckoutController extends Controller
{
    public function show(Request $request): RedirectResponse|View
    {
        $user = $request->user();
        
        $cart = $request->session()->get('cart', []);
        if (empty($cart)) {
            return redirect()->route('cart.show')->withErrors(['cart' => 'Your cart is empty.']);
        }

        // Calculate cart totals
        $subtotal = collect($cart)->sum(function ($item) {
            return ($item['price'] ?? 0) * ($item['qty'] ?? 1);
        });
        
        $discount = 0;
        $coupon = null;
        $couponCode = $request->session()->get('coupon_code');
        if ($couponCode) {
            $coupon = Coupon::where('code', $couponCode)->first();
            if ($coupon && $user) {
                $validation = $coupon->isValid($user->id, $subtotal);
                if ($validation['valid']) {
                    $discount = $coupon->calculateDiscount($subtotal);
                }
            }
        }
        
        $coinDiscount = 0;
        $coinsToUse = $request->session()->get('coins_to_use', 0);
        if ($coinsToUse > 0 && $user) {
            $pointsPerDollar = (int) Setting::get('points_per_dollar', 1000);
            $coinDiscount = $coinsToUse / $pointsPerDollar;
        }
        
        $total = $subtotal - $discount - $coinDiscount;
        $userPoints = $user ? $user->getPoints() : 0;

        return view('checkout', [
            'title' => 'Checkout - SketchUp Collection',
            'metaDescription' => 'Secure checkout',
            'user' => $user,
            'cart' => $cart,
            'subtotal' => $subtotal,
            'discount' => $discount,
            'coinDiscount' => $coinDiscount,
            'coinsToUse' => $coinsToUse,
            'total' => $total,
            'userPoints' => $userPoints,
            'coupon' => $coupon,
        ]);
    }

    /**
     * Create a Stripe Checkout Session from the current cart and redirect.
     */
    public function stripeStart(Request $request): RedirectResponse
    {
        $cart = $request->session()->get('cart', []);

        if (empty($cart)) {
            return back()->withErrors(['payment' => 'Your cart is empty.']);
        }

        $secret = Setting::get('stripe_key') ?? config('services.stripe.secret') ?? env('STRIPE_KEY');
        if (!$secret) {
            return back()->withErrors(['payment' => 'Stripe key not configured.']);
        }

        // Calculate subtotal and discount
        $subtotal = collect($cart)->sum(function ($item) {
            return ($item['price'] ?? 0) * ($item['qty'] ?? 1);
        });
        
        $discount = 0;
        $coupon = null;
        $couponCode = $request->session()->get('coupon_code');
        if ($couponCode) {
            $coupon = Coupon::where('code', $couponCode)->first();
            if ($coupon) {
                $validation = $coupon->isValid($request->user()?->id, $subtotal);
                if ($validation['valid']) {
                    $discount = $coupon->calculateDiscount($subtotal);
                } else {
                    // Invalid coupon, remove it
                    $request->session()->forget('coupon_code');
                }
            }
        }
        
        // Calculate coin discount
        $coinDiscount = 0;
        $coinsToUse = $request->session()->get('coins_to_use', 0);
        $user = $request->user();
        if ($coinsToUse > 0 && $user) {
            $pointsPerDollar = (int) Setting::get('points_per_dollar', 1000);
            $maxCoinValue = ($subtotal - $discount) * $pointsPerDollar;
            $coinsToUse = min($coinsToUse, $maxCoinValue, $user->getPoints());
            $coinDiscount = $coinsToUse / $pointsPerDollar;
            // Update session with validated coins
            $request->session()->put('coins_to_use', $coinsToUse);
        }
        
        $total = $subtotal - $discount - $coinDiscount;
        
        // For Stripe, we need to adjust line items proportionally to reflect the discount
        // Calculate the discount ratio
        $discountRatio = $subtotal > 0 ? ($total / $subtotal) : 1;

        $lineItems = collect($cart)
            ->map(function ($item) use ($discountRatio) {
                $price = (float)($item['price'] ?? 0);
                $qty = (int)($item['qty'] ?? 1);
                if ($price <= 0 || $qty <= 0) {
                    return null;
                }
                // Apply discount proportionally
                $adjustedPrice = $price * $discountRatio;
                return [
                    'price_data' => [
                        'currency' => 'usd',
                        'product_data' => [
                            'name' => $item['title'] ?? 'Item',
                        ],
                        'unit_amount' => (int)round($adjustedPrice * 100),
                    ],
                    'quantity' => $qty,
                ];
            })
            ->filter()
            ->values()
            ->all();

        if (empty($lineItems)) {
            return back()->withErrors(['payment' => 'Cart items are invalid for checkout.']);
        }

        $client = new StripeClient($secret);

        try {
            $session = $client->checkout->sessions->create([
                'payment_method_types' => ['card'],
                'line_items' => $lineItems,
                'mode' => 'payment',
                'success_url' => route('checkout.success', [], true) . '?session_id={CHECKOUT_SESSION_ID}',
                'cancel_url' => route('checkout.cancel', [], true),
                'client_reference_id' => Auth::id(),
            ]);
        } catch (\Throwable $e) {
            return back()->withErrors(['payment' => $e->getMessage()]);
        }

        return redirect()->away($session->url);
    }

    public function success(Request $request): RedirectResponse
    {
        $user = $request->user();
        
        if (!$user) {
            return redirect()->route('checkout.show')->withErrors(['payment' => 'You must be logged in to complete checkout.']);
        }

        $sessionId = $request->query('session_id');
        
        if (!$sessionId) {
            return redirect()->route('checkout.show')->withErrors(['payment' => 'Invalid checkout session.']);
        }

        $secret = Setting::get('stripe_key') ?? config('services.stripe.secret') ?? env('STRIPE_KEY');
        if (!$secret) {
            return redirect()->route('checkout.show')->withErrors(['payment' => 'Stripe key not configured.']);
        }

        $client = new StripeClient($secret);

        try {
            // Retrieve the Stripe checkout session to verify payment
            $session = $client->checkout->sessions->retrieve($sessionId);
            
            // Verify payment was successful
            if ($session->payment_status !== 'paid') {
                return redirect()->route('checkout.show')->withErrors(['payment' => 'Payment was not completed.']);
            }

            // Check if this session has already been processed (prevent duplicate orders)
            $paymentIntentId = $session->payment_intent ?? $sessionId;
            $existingTransaction = Transaction::where('gateway_transaction_id', $paymentIntentId)
                ->where('status', 'completed')
                ->first();
            
            if ($existingTransaction) {
                return redirect()->route('checkout.show')->with('status', 'Payment successful! Your order has already been processed.');
            }

            // Get cart from session
            $cart = $request->session()->get('cart', []);
            
            if (empty($cart)) {
                // Cart might be empty if order was already processed
                return redirect()->route('checkout.show')->with('status', 'Payment successful!');
            }

            // Validate all products in cart still exist (product_id is required in order_items)
            $productIds = collect($cart)->pluck('id')->filter()->unique()->all();
            $existingProducts = Product::whereIn('id', $productIds)->get()->keyBy('id');
            
            // Check if any products are missing
            $missingProductIds = array_diff($productIds, $existingProducts->keys()->all());
            if (!empty($missingProductIds)) {
                Log::error('Products in cart no longer exist', ['missing_ids' => $missingProductIds]);
                return redirect()->route('checkout.show')->withErrors(['payment' => 'Some products in your cart are no longer available. Please update your cart and try again.']);
            }

            // Calculate totals
            $subtotal = collect($cart)->sum(function ($item) {
                return ($item['price'] ?? 0) * ($item['qty'] ?? 1);
            });
            $tax = 0; // Add tax calculation if needed
            
            // Apply coupon discount if exists
            $discount = 0;
            $coupon = null;
            $couponCode = $request->session()->get('coupon_code');
            if ($couponCode) {
                $coupon = Coupon::where('code', $couponCode)->first();
                if ($coupon) {
                    $validation = $coupon->isValid($user->id, $subtotal);
                    if ($validation['valid']) {
                        $discount = $coupon->calculateDiscount($subtotal);
                    } else {
                        // Invalid coupon, remove it
                        $request->session()->forget('coupon_code');
                    }
                }
            }
            
            // Apply coin discount if exists
            $coinDiscount = 0;
            $coinsToUse = $request->session()->get('coins_to_use', 0);
            if ($coinsToUse > 0) {
                $pointsPerDollar = (int) Setting::get('points_per_dollar', 1000);
                $maxCoinValue = ($subtotal - $discount) * $pointsPerDollar;
                $coinsToUse = min($coinsToUse, $maxCoinValue, $user->getPoints());
                $coinDiscount = $coinsToUse / $pointsPerDollar;
            }
            
            $total = $subtotal + $tax - $discount - $coinDiscount;

            // Create order and order items in a transaction
            DB::beginTransaction();
            
            try {
                $order = Order::create([
                    'user_id' => $user->id,
                    'status' => 'completed',
                    'subtotal' => $subtotal,
                    'tax' => $tax,
                    'discount' => $discount + $coinDiscount,
                    'total' => $total,
                    'currency' => 'USD',
                    'payment_method' => 'stripe',
                    'payment_status' => 'paid',
                    'customer_email' => $user->email,
                    'customer_name' => $user->name,
                ]);

                // Deduct coins from user balance if coins were used
                if ($coinsToUse > 0 && $coinDiscount > 0) {
                    $user->deductPoints(
                        $coinsToUse,
                        'purchase',
                        "Used {$coinsToUse} SKP coins for Order #{$order->order_number}",
                        $order->id,
                        'order'
                    );
                    // Clear coins from session after successful use
                    $request->session()->forget('coins_to_use');
                }

                // Create order items - all products are validated to exist
                foreach ($cart as $item) {
                    $productId = $item['id'] ?? null;
                    $product = $existingProducts->get($productId);
                    
                    if (!$product) {
                        // This shouldn't happen due to validation above, but safety check
                        Log::error('Product not found during order item creation', ['product_id' => $productId]);
                        throw new \Exception("Product with ID {$productId} not found during order creation.");
                    }
                    
                    OrderItem::create([
                        'order_id' => $order->id,
                        'product_id' => $product->id,
                        'product_name' => $item['title'] ?? $product->title,
                        'product_slug' => $item['slug'] ?? $product->slug,
                        'product_image' => $item['image'] ?? $product->image_url,
                        'quantity' => (int)($item['qty'] ?? 1),
                        'price' => (float)($item['price'] ?? $product->price),
                        'total' => (float)($item['price'] ?? $product->price) * (int)($item['qty'] ?? 1),
                    ]);
                }

                // Create transaction record
                Transaction::create([
                    'order_id' => $order->id,
                    'user_id' => $user->id,
                    'amount' => $total,
                    'currency' => 'USD',
                    'type' => 'payment',
                    'status' => 'completed',
                    'payment_method' => 'card',
                    'payment_gateway' => 'stripe',
                    'gateway_transaction_id' => $session->payment_intent ?? $sessionId,
                    'gateway_response' => [
                        'session_id' => $sessionId,
                        'payment_status' => $session->payment_status,
                        'customer_email' => $session->customer_details->email ?? $user->email,
                    ],
                    'description' => 'Order payment via Stripe',
                    'completed_at' => now(),
                ]);

                // Track coupon usage if coupon was applied
                if ($coupon && $discount > 0) {
                    CouponUsage::create([
                        'coupon_id' => $coupon->id,
                        'user_id' => $user->id,
                        'order_id' => $order->id,
                        'discount_amount' => $discount,
                        'email' => $user->email,
                    ]);

                    // Increment coupon used count
                    $coupon->increment('used_count');

                    // Remove coupon from session after successful use
                    $request->session()->forget('coupon_code');
                }

                // Handle referral rewards if user was referred
                if ($user->referred_by) {
                    $referral = Referral::where('referrer_id', $user->referred_by)
                        ->where('referred_id', $user->id)
                        ->where('status', 'pending')
                        ->first();

                    if ($referral) {
                        // Calculate referral reward percentage (default 10%, configurable in admin)
                        $rewardPercentage = (float) Setting::get('referral_commission_percentage', 10) / 100;
                        $rewardAmount = $total * $rewardPercentage;

                        // Convert reward to points (1$ = 1000 coins)
                        $pointsPerDollar = (int) Setting::get('points_per_dollar', 1000);
                        $rewardPoints = (int) round($rewardAmount * $pointsPerDollar);

                        // Mark referral as completed and reward
                        $referral->markAsRewarded($rewardAmount, 'purchase');

                        // Add earnings to referrer (in dollars)
                        $referrer = $user->referrer;
                        if ($referrer && $rewardPoints > 0) {
                            $referrer->addReferralEarnings($rewardAmount);
                            // Award points to referrer
                            $referrer->addPoints(
                                $rewardPoints,
                                'referral',
                                "Referral commission from {$user->name}'s purchase (Order #{$order->order_number})",
                                $order->id,
                                'order'
                            );
                        }
                    }
                }

                DB::commit();

                // Clear cart after successful order
                $request->session()->forget('cart');

                return redirect()->route('checkout.show')->with('status', 'Payment successful! Your order has been placed.');

            } catch (\Throwable $e) {
                DB::rollBack();
                Log::error('Order creation failed: ' . $e->getMessage());
                return redirect()->route('checkout.show')->withErrors(['payment' => 'Failed to create order. Please contact support.']);
            }

        } catch (\Throwable $e) {
            Log::error('Stripe session retrieval failed: ' . $e->getMessage());
            return redirect()->route('checkout.show')->withErrors(['payment' => 'Failed to verify payment. Please contact support.']);
        }
    }

    public function cancel(): RedirectResponse
    {
        return redirect()->route('checkout.show')->withErrors(['payment' => 'Payment was cancelled.']);
    }
}

