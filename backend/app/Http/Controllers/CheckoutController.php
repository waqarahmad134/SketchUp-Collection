<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Stripe\StripeClient;

class CheckoutController extends Controller
{
    public function show(Request $request): RedirectResponse|View
    {
        $user = $request->user();

        return view('checkout', [
            'title' => 'Checkout - SketchUp Collection',
            'metaDescription' => 'Secure checkout',
            'user' => $user,
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

        $secret = config('services.stripe.secret') ?? env('STRIPE_KEY');
        if (!$secret) {
            return back()->withErrors(['payment' => 'Stripe key not configured.']);
        }

        $lineItems = collect($cart)
            ->map(function ($item) {
                $price = (float)($item['price'] ?? 0);
                $qty = (int)($item['qty'] ?? 1);
                if ($price <= 0 || $qty <= 0) {
                    return null;
                }
                return [
                    'price_data' => [
                        'currency' => 'usd',
                        'product_data' => [
                            'name' => $item['title'] ?? 'Item',
                        ],
                        'unit_amount' => (int)round($price * 100),
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
                'success_url' => route('checkout.success', [], true),
                'cancel_url' => route('checkout.cancel', [], true),
                'client_reference_id' => Auth::id(),
            ]);
        } catch (\Throwable $e) {
            return back()->withErrors(['payment' => $e->getMessage()]);
        }

        return redirect()->away($session->url);
    }

    public function success(): RedirectResponse
    {
        return redirect()->route('checkout.show')->with('status', 'Payment successful (demo redirect).');
    }

    public function cancel(): RedirectResponse
    {
        return redirect()->route('checkout.show')->withErrors(['payment' => 'Payment was cancelled.']);
    }
}

