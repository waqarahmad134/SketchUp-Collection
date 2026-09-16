<?php

namespace App\Http\Controllers;

use App\Models\Coupon;
use App\Models\PaymentAttempt;
use App\Models\PaymentGateway;
use App\Models\Setting;
use App\Services\OrderService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * Storefront payment flow for admin-configured gateways.
 */
class PaymentController extends Controller
{
    public function __construct(protected OrderService $orders) {}

    protected function findGateway(string $slug): PaymentGateway
    {
        return PaymentGateway::where('slug', $slug)->where('is_enabled', true)->firstOrFail();
    }

    /**
     * Start a payment: snapshot the cart into an attempt, then hand off to the driver.
     */
    public function start(Request $request, string $gateway)
    {
        $gatewayModel = $this->findGateway($gateway);
        $user = $request->user();

        if (! $user) {
            return redirect()->route('login')->withErrors(['payment' => 'Please log in to complete your purchase.']);
        }

        $cart = $request->session()->get('cart', []);
        if (empty($cart)) {
            return redirect()->route('cart.show')->withErrors(['cart' => 'Your cart is empty.']);
        }

        $totals = $this->cartTotals($request, $cart, $user);
        if ($totals['total'] <= 0) {
            return back()->withErrors(['payment' => 'Nothing to charge.']);
        }

        $attempt = PaymentAttempt::create([
            'gateway_slug' => $gatewayModel->slug,
            'user_id' => $user->id,
            'cart' => $cart,
            'subtotal' => $totals['subtotal'],
            'tax' => $totals['tax'],
            'discount' => $totals['discount'],
            'total' => $totals['total'],
            'currency' => 'USD',
            'coupon_code' => $request->session()->get('coupon_code'),
            'coins_to_use' => $request->session()->get('coins_to_use', 0),
        ]);

        try {
            $result = $gatewayModel->driver()->initiate($attempt);
        } catch (\Throwable $e) {
            Log::error('Payment initiation failed', ['gateway' => $gateway, 'error' => $e->getMessage()]);
            $attempt->update(['status' => 'failed']);
            return back()->withErrors(['payment' => $e->getMessage()]);
        }

        if (isset($result['form'])) {
            return view('payment-redirect', [
                'title' => 'Redirecting to payment...',
                'action' => $result['form']['action'],
                'fields' => $result['form']['fields'],
                'gatewayName' => $gatewayModel->name,
            ]);
        }

        return redirect()->away($result['redirect']);
    }

    /**
     * Browser return from the provider (JazzCash/Easypaisa/PayPro POST here,
     * hosted checkouts GET here).
     */
    public function callback(Request $request, string $gateway)
    {
        $gatewayModel = $this->findGateway($gateway);

        try {
            $result = $gatewayModel->driver()->verifyCallback($request);
        } catch (\Throwable $e) {
            Log::error('Payment callback error', ['gateway' => $gateway, 'error' => $e->getMessage()]);
            return redirect()->route('checkout.show')->withErrors(['payment' => 'Payment verification failed. Please contact support.']);
        }

        if (! ($result['ok'] ?? false) || empty($result['attempt_token'])) {
            return redirect()->route('checkout.show')->withErrors(['payment' => 'Payment was not completed.']);
        }

        $attempt = PaymentAttempt::where('token', $result['attempt_token'])
            ->where('gateway_slug', $gateway)
            ->first();

        if (! $attempt || $attempt->status !== 'pending') {
            return redirect()->route('checkout.show')->with('status', 'Payment received. Your order is being processed.');
        }

        $order = $this->orders->completeFromAttempt($attempt, [
            'gateway' => $gateway,
            'transaction_id' => $result['gateway_reference'] ?? $attempt->token,
            'amount' => $result['amount'],
            'currency' => $gatewayModel->driver()->chargeCurrency(),
            'method' => $gateway,
            'response' => $result['raw'] ?? [],
            'description' => "Order payment via {$gatewayModel->name}",
        ], $request->session());

        if ($order) {
            return redirect()->route('checkout.show')->with('status', 'Payment successful! Your order has been placed.');
        }

        return redirect()->route('checkout.show')->with('status', 'Payment received. Your order is being processed.');
    }

    /**
     * Server-to-server webhook (Paddle, Lemon Squeezy, Polar).
     */
    public function webhook(Request $request, string $gateway)
    {
        $gatewayModel = PaymentGateway::where('slug', $gateway)->where('is_enabled', true)->first();

        if (! $gatewayModel) {
            return response()->json(['error' => 'unknown gateway'], 404);
        }

        $result = $gatewayModel->driver()->verifyWebhook($request);

        if (! ($result['ok'] ?? false) || empty($result['attempt_token'])) {
            return response()->json(['received' => true]);
        }

        $attempt = PaymentAttempt::where('token', $result['attempt_token'])
            ->where('gateway_slug', $gateway)
            ->first();

        if ($attempt && $attempt->status === 'pending') {
            $this->orders->completeFromAttempt($attempt, [
                'gateway' => $gateway,
                'transaction_id' => $result['gateway_reference'] ?? $attempt->token,
                'amount' => $result['amount'],
                'currency' => $gatewayModel->driver()->chargeCurrency(),
                'method' => $gateway,
                'response' => $result['raw'] ?? [],
                'description' => "Order payment via {$gatewayModel->name} (webhook)",
            ]);
        }

        return response()->json(['received' => true]);
    }

    /**
     * Same totals math as the Stripe checkout flow.
     */
    protected function cartTotals(Request $request, array $cart, $user): array
    {
        $subtotal = collect($cart)->sum(fn ($item) => ($item['price'] ?? 0) * ($item['qty'] ?? 1));
        $tax = 0;

        $discount = 0;
        $couponCode = $request->session()->get('coupon_code');
        if ($couponCode) {
            $coupon = Coupon::where('code', $couponCode)->first();
            if ($coupon && $user) {
                $validation = $coupon->isValid($user->id, $subtotal);
                if ($validation['valid']) {
                    $discount = $coupon->calculateDiscount($subtotal);
                } else {
                    $request->session()->forget('coupon_code');
                }
            }
        }

        $coinDiscount = 0;
        $coinsToUse = $request->session()->get('coins_to_use', 0);
        if ($coinsToUse > 0 && $user) {
            $pointsPerDollar = (int) Setting::get('points_per_dollar', 1000);
            $maxCoinValue = ($subtotal - $discount) * $pointsPerDollar;
            $coinsToUse = min($coinsToUse, $maxCoinValue, $user->getPoints());
            $coinDiscount = $coinsToUse / $pointsPerDollar;
            $request->session()->put('coins_to_use', $coinsToUse);
        }

        return [
            'subtotal' => $subtotal,
            'tax' => $tax,
            'discount' => $discount + $coinDiscount,
            'total' => $subtotal + $tax - $discount - $coinDiscount,
        ];
    }
}
