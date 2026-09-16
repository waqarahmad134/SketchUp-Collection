<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class CurrencyController extends Controller
{
    /**
     * Switch the display currency (USD/PKR) stored in the session.
     */
    public function switch(Request $request): RedirectResponse
    {
        $currency = strtoupper($request->input('currency', 'USD'));

        if (in_array($currency, ['USD', 'PKR'])) {
            session(['currency' => $currency]);
        }

        return back();
    }
}
