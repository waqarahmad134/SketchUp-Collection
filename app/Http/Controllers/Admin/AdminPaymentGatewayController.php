<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PaymentGateway;
use App\Models\Setting;
use Illuminate\Http\Request;

class AdminPaymentGatewayController extends Controller
{
    public function index()
    {
        $gateways = PaymentGateway::orderBy('sort_order')->get();
        $usdToPkr = Setting::get('usd_to_pkr_rate', config('payment-gateways.usd_to_pkr_rate', 278));

        return view('admin.payment-gateways.index', compact('gateways', 'usdToPkr'));
    }

    public function edit(PaymentGateway $gateway)
    {
        $definition = $gateway->definition();

        return view('admin.payment-gateways.edit', compact('gateway', 'definition'));
    }

    public function update(Request $request, PaymentGateway $gateway)
    {
        $definition = $gateway->definition();
        $rules = ['test_mode' => 'nullable|boolean'];

        foreach ($definition['fields'] ?? [] as $field) {
            // Always nullable here: required-ness is enforced against stored
            // values below so saved secrets can be left blank to keep them.
            $rules["credentials.{$field['key']}"] = 'nullable|string|max:2000';
        }

        $validated = $request->validate($rules);

        // Merge credentials: keep existing secrets when the field is left blank
        $credentials = $gateway->credentials ?? [];
        foreach ($definition['fields'] ?? [] as $field) {
            $key = $field['key'];
            $value = trim($validated['credentials'][$key] ?? '');
            if ($value !== '') {
                $credentials[$key] = $value;
            } elseif (($field['required'] ?? false) && empty($credentials[$key])) {
                return back()->withErrors(["credentials.{$key}" => "The {$field['label']} field is required."])->withInput();
            }
        }

        $gateway->update([
            'credentials' => $credentials,
            'test_mode' => $request->boolean('test_mode'),
        ]);

        return redirect()->route('admin.payment-gateways.index')
            ->with('success', "{$gateway->name} settings saved.");
    }

    public function toggle(PaymentGateway $gateway)
    {
        $gateway->update(['is_enabled' => ! $gateway->is_enabled]);

        $state = $gateway->is_enabled ? 'enabled' : 'disabled';

        return back()->with('success', "{$gateway->name} {$state}.");
    }

    public function updateRate(Request $request)
    {
        $validated = $request->validate([
            'usd_to_pkr_rate' => 'required|numeric|min:1|max:10000',
        ]);

        Setting::set('usd_to_pkr_rate', $validated['usd_to_pkr_rate']);

        return back()->with('success', 'Exchange rate updated.');
    }
}
