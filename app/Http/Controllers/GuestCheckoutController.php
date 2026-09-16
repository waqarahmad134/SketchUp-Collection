<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class GuestCheckoutController extends Controller
{
    /**
     * Let a guest check out with just name + email: find or create their
     * account, log them in, and send them to the normal checkout flow.
     * New accounts must set a password from their account dashboard.
     */
    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255'],
        ]);

        $user = User::where('email', $data['email'])->first();

        if (! $user) {
            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Str::random(32),
                'must_set_password' => true,
            ]);
        }

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()
            ->route('checkout.show')
            ->with('status', $user->wasRecentlyCreated
                ? 'An account was created for you. You can set a password anytime from your account dashboard.'
                : 'Welcome back! You are signed in.');
    }
}
