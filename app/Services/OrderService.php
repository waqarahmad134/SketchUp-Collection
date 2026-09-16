<?php

namespace App\Services;

use App\Models\Coupon;
use App\Models\CouponUsage;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\PaymentAttempt;
use App\Models\Product;
use App\Models\Referral;
use App\Models\Setting;
use App\Models\Transaction;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Creates a completed order from a verified payment attempt.
 * Shared by all payment gateway callbacks/webhooks.
 */
class OrderService
{
    /**
     * @param PaymentAttempt $attempt  Pending attempt with cart snapshot
     * @param array $payment  ['gateway' => slug, 'transaction_id' => string,
     *                        'amount' => float, 'currency' => string,
     *                        'method' => string, 'response' => array, 'description' => string]
     * @param \Illuminate\Session\Store|null $session  Cleared on success when available
     */
    public function completeFromAttempt(PaymentAttempt $attempt, array $payment, $session = null): ?Order
    {
        return DB::transaction(function () use ($attempt, $payment, $session) {
            // Lock the attempt; only a pending attempt can be completed (idempotency).
            $attempt = PaymentAttempt::where('id', $attempt->id)
                ->where('status', 'pending')
                ->lockForUpdate()
                ->first();

            if (! $attempt) {
                Log::info('Payment attempt already processed', ['attempt_id' => $attempt->id ?? null]);
                return null;
            }

            $user = $attempt->user;
            $cart = $attempt->cart ?? [];

            if (! $user || empty($cart)) {
                throw new \RuntimeException('Invalid payment attempt.');
            }

            // Validate products still exist
            $productIds = collect($cart)->pluck('id')->filter()->unique()->all();
            $existingProducts = Product::whereIn('id', $productIds)->get()->keyBy('id');
            $missing = array_diff($productIds, $existingProducts->keys()->all());
            if (! empty($missing)) {
                throw new \RuntimeException('Some products are no longer available.');
            }

            $subtotal = collect($cart)->sum(fn ($item) => ($item['price'] ?? 0) * ($item['qty'] ?? 1));
            $tax = 0;

            // Coupon
            $discount = 0;
            $coupon = null;
            if ($attempt->coupon_code) {
                $coupon = Coupon::where('code', $attempt->coupon_code)->first();
                if ($coupon) {
                    $validation = $coupon->isValid($user->id, $subtotal);
                    if ($validation['valid']) {
                        $discount = $coupon->calculateDiscount($subtotal);
                    } else {
                        $coupon = null;
                    }
                }
            }

            // Coins
            $coinDiscount = 0;
            $coinsToUse = (int) $attempt->coins_to_use;
            if ($coinsToUse > 0) {
                $pointsPerDollar = (int) Setting::get('points_per_dollar', 1000);
                $maxCoinValue = ($subtotal - $discount) * $pointsPerDollar;
                $coinsToUse = (int) min($coinsToUse, $maxCoinValue, $user->getPoints());
                $coinDiscount = $coinsToUse / $pointsPerDollar;
            }

            $total = $subtotal + $tax - $discount - $coinDiscount;
            $gateway = $payment['gateway'];

            $order = Order::create([
                'user_id' => $user->id,
                'status' => 'completed',
                'subtotal' => $subtotal,
                'tax' => $tax,
                'discount' => $discount + $coinDiscount,
                'total' => $total,
                'currency' => $payment['currency'] ?? 'USD',
                'payment_method' => $gateway,
                'payment_status' => 'paid',
                'customer_email' => $user->email,
                'customer_name' => $user->name,
            ]);

            if ($coinsToUse > 0 && $coinDiscount > 0) {
                $user->deductPoints(
                    $coinsToUse,
                    'purchase',
                    "Used {$coinsToUse} SKP coins for Order #{$order->order_number}",
                    $order->id,
                    'order'
                );
            }

            foreach ($cart as $item) {
                $product = $existingProducts->get($item['id'] ?? null);
                if (! $product) {
                    continue;
                }

                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'product_name' => $item['title'] ?? $product->title,
                    'product_slug' => $item['slug'] ?? $product->slug,
                    'product_image' => $item['image'] ?? $product->image_url,
                    'quantity' => (int) ($item['qty'] ?? 1),
                    'price' => (float) ($item['price'] ?? $product->price),
                    'total' => (float) ($item['price'] ?? $product->price) * (int) ($item['qty'] ?? 1),
                ]);
            }

            Transaction::create([
                'order_id' => $order->id,
                'user_id' => $user->id,
                'amount' => $total,
                'currency' => $payment['currency'] ?? 'USD',
                'type' => 'payment',
                'status' => 'completed',
                'payment_method' => $payment['method'] ?? $gateway,
                'payment_gateway' => $gateway,
                'gateway_transaction_id' => $payment['transaction_id'],
                'gateway_response' => $payment['response'] ?? [],
                'description' => $payment['description'] ?? "Order payment via {$gateway}",
                'completed_at' => now(),
            ]);

            if ($coupon && $discount > 0) {
                CouponUsage::create([
                    'coupon_id' => $coupon->id,
                    'user_id' => $user->id,
                    'order_id' => $order->id,
                    'discount_amount' => $discount,
                    'email' => $user->email,
                ]);
                $coupon->increment('used_count');
            }

            // Referral rewards
            if ($user->referred_by) {
                $referral = Referral::where('referrer_id', $user->referred_by)
                    ->where('referred_id', $user->id)
                    ->where('status', 'pending')
                    ->first();

                if ($referral) {
                    $rewardPercentage = (float) Setting::get('referral_commission_percentage', 10) / 100;
                    $rewardAmount = $total * $rewardPercentage;
                    $pointsPerDollar = (int) Setting::get('points_per_dollar', 1000);
                    $rewardPoints = (int) round($rewardAmount * $pointsPerDollar);

                    $referral->markAsRewarded($rewardAmount, 'purchase');

                    $referrer = $user->referrer;
                    if ($referrer && $rewardPoints > 0) {
                        $referrer->addReferralEarnings($rewardAmount);
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

            $attempt->markCompleted($payment['transaction_id']);

            if ($session) {
                $session->forget(['cart', 'coupon_code', 'coins_to_use']);
            }

            return $order;
        });
    }
}
