<?php

namespace App\Http\Controllers;

use App\Models\NewsletterSubscriber;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class NewsletterController extends Controller
{
    public function subscribe(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'email' => ['required', 'email', 'max:255'],
            'name' => ['nullable', 'string', 'max:255'],
        ]);

        $subscriber = NewsletterSubscriber::firstOrNew(['email' => $data['email']]);
        $wasActive = $subscriber->exists && $subscriber->is_active;

        $subscriber->name = $data['name'] ?? $subscriber->name;
        $subscriber->is_active = true;
        $subscriber->save();

        $message = $wasActive
            ? 'You are already subscribed. Thank you!'
            : 'Subscribed! Watch your inbox for new bundles and exclusive discounts.';

        return back()->with('status', $message);
    }

    public function unsubscribe(Request $request, string $token): RedirectResponse
    {
        $subscriber = NewsletterSubscriber::where('unsubscribe_token', $token)->first();

        if ($subscriber) {
            $subscriber->is_active = false;
            $subscriber->save();
        }

        return redirect()->route('home')->with('status', 'You have been unsubscribed.');
    }
}
