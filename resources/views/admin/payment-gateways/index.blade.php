@extends('layouts.admin')

@section('title', 'Payment Gateways')

@section('content')
<div class="p-6 md:p-8">
    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-3xl font-bold text-foreground mb-2">Payment Gateways</h1>
            <p class="text-muted-foreground">Enable or disable gateways and manage their API credentials. Customers only see enabled gateways at checkout.</p>
        </div>
    </div>

    @if(session('success'))
        <div class="mb-6 px-4 py-3 rounded-xl bg-green-500/10 text-green-400 text-sm">{{ session('success') }}</div>
    @endif

    <!-- USD to PKR rate -->
    <div class="shadcn-card rounded-xl border bg-card border-card-border text-card-foreground shadow-sm p-6 mb-6">
        <h2 class="text-lg font-semibold mb-2">Currency conversion</h2>
        <p class="text-sm text-muted-foreground mb-4">PKR gateways (JazzCash, Easypaisa, PayPro) charge in rupees. Your cart totals are in USD, converted with this rate.</p>
        <form method="POST" action="{{ route('admin.payment-gateways.rate') }}" class="flex items-end gap-3 max-w-md">
            @csrf
            <div class="flex-1">
                <label class="block text-sm font-medium mb-2">1 USD = ? PKR</label>
                <input type="number" step="0.01" min="1" name="usd_to_pkr_rate" value="{{ old('usd_to_pkr_rate', $usdToPkr) }}" required
                    class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring">
            </div>
            <button type="submit" class="inline-flex items-center justify-center rounded-md text-sm font-medium h-10 px-4 bg-primary text-primary-foreground hover-elevate">Save rate</button>
        </form>
    </div>

    <!-- Gateways table -->
    <div class="shadcn-card rounded-xl border bg-card border-card-border text-card-foreground shadow-sm overflow-hidden">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-card-border text-left text-muted-foreground">
                    <th class="px-6 py-4 font-medium">Gateway</th>
                    <th class="px-6 py-4 font-medium">Currency</th>
                    <th class="px-6 py-4 font-medium">Mode</th>
                    <th class="px-6 py-4 font-medium">Status</th>
                    <th class="px-6 py-4 font-medium text-right">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($gateways as $gateway)
                    @php $definition = $gateway->definition(); @endphp
                    <tr class="border-b border-card-border last:border-0">
                        <td class="px-6 py-4">
                            <div class="font-semibold">{{ $gateway->name }}</div>
                            <div class="text-xs text-muted-foreground">{{ $definition['description'] ?? '' }}</div>
                        </td>
                        <td class="px-6 py-4">{{ $definition['currency'] ?? 'USD' }}</td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $gateway->test_mode ? 'bg-amber-500/15 text-amber-400' : 'bg-green-500/15 text-green-400' }}">
                                {{ $gateway->test_mode ? 'Test' : 'Live' }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <form method="POST" action="{{ route('admin.payment-gateways.toggle', $gateway) }}" class="inline">
                                @csrf
                                <button type="submit" title="{{ $gateway->is_enabled ? 'Disable' : 'Enable' }}"
                                    class="relative inline-flex h-6 w-11 items-center rounded-full transition {{ $gateway->is_enabled ? 'bg-green-500' : 'bg-muted' }}">
                                    <span class="inline-block h-4 w-4 transform rounded-full bg-white transition {{ $gateway->is_enabled ? 'translate-x-6' : 'translate-x-1' }}"></span>
                                </button>
                            </form>
                            <span class="ml-2 text-xs {{ $gateway->is_enabled ? 'text-green-400' : 'text-muted-foreground' }}">
                                {{ $gateway->is_enabled ? 'Enabled' : 'Disabled' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-right whitespace-nowrap">
                            @if(!empty($definition['docs_url']))
                                <a href="{{ $definition['docs_url'] }}" target="_blank" rel="noopener"
                                    class="inline-flex items-center justify-center rounded-md text-sm font-medium h-9 px-4 mr-2 text-cyan-400 hover:underline">
                                    {{ $definition['docs_label'] ?? 'Docs' }}
                                    <svg class="w-3.5 h-3.5 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                                </a>
                            @endif
                            <a href="{{ route('admin.payment-gateways.edit', $gateway) }}"
                                class="inline-flex items-center justify-center rounded-md text-sm font-medium h-9 px-4 border border-input bg-background hover-elevate">
                                Settings
                            </a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <p class="mt-4 text-xs text-muted-foreground">You can enable multiple gateways at once, or just one. Disabled gateways are hidden from customers.</p>
</div>
@endsection
