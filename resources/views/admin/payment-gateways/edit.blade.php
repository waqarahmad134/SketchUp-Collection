@extends('layouts.admin')

@section('title', $gateway->name . ' Settings')

@section('content')
<div class="p-6 md:p-8 max-w-3xl">
    <div class="flex items-center gap-4 mb-6">
        <a href="{{ route('admin.payment-gateways.index') }}"
            class="inline-flex items-center justify-center rounded-md text-sm font-medium h-9 w-9 border border-input bg-background hover-elevate">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
        </a>
        <div>
            <h1 class="text-3xl font-bold text-foreground">{{ $gateway->name }} Settings</h1>
            <p class="text-muted-foreground">{{ $definition['description'] ?? '' }}</p>
            @if(!empty($definition['docs_url']))
                <a href="{{ $definition['docs_url'] }}" target="_blank" rel="noopener" class="inline-flex items-center gap-1 mt-2 text-sm text-cyan-400 hover:underline">
                    {{ $definition['docs_label'] ?? 'Docs' }}
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                </a>
            @endif
        </div>
    </div>

    @if($definition['setup_help'] ?? false)
        <div class="mb-6 px-4 py-3 rounded-xl bg-cyan-500/10 text-cyan-300 text-sm">{{ $definition['setup_help'] }}</div>
    @endif

    @if($errors->any())
        <div class="mb-6 px-4 py-3 rounded-xl bg-red-500/10 text-red-400 text-sm">
            <ul class="list-disc list-inside">
                @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('admin.payment-gateways.update', $gateway) }}">
        @csrf
        @method('PUT')

        <div class="shadcn-card rounded-xl border bg-card border-card-border text-card-foreground shadow-sm p-6 mb-6">
            <h2 class="text-lg font-semibold mb-4">API Credentials</h2>
            <div class="space-y-4">
                @foreach($definition['fields'] ?? [] as $field)
                    <div>
                        <label class="block text-sm font-medium mb-2">
                            {{ $field['label'] }} @if($field['required'] ?? false)<span class="text-red-400">*</span>@endif
                        </label>
                        <input
                            type="{{ $field['type'] ?? 'text' }}"
                            name="credentials[{{ $field['key'] }}]"
                            value="{{ $field['type'] === 'password' ? '' : old('credentials.' . $field['key'], ($gateway->credentials[$field['key']] ?? '')) }}"
                            @if($field['type'] === 'password') placeholder="{{ !empty($gateway->credentials[$field['key']]) ? 'Saved (leave blank to keep)' : '' }}" autocomplete="new-password" @endif
                            class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring"
                        >
                        @if(!empty($field['help']))<p class="mt-1 text-xs text-muted-foreground">{{ $field['help'] }}</p>@endif
                    </div>
                @endforeach
            </div>
        </div>

        <div class="shadcn-card rounded-xl border bg-card border-card-border text-card-foreground shadow-sm p-6 mb-6">
            <h2 class="text-lg font-semibold mb-4">Mode</h2>
            <label class="flex items-center gap-3 cursor-pointer">
                <input type="checkbox" name="test_mode" value="1" {{ old('test_mode', $gateway->test_mode) ? 'checked' : '' }} class="h-4 w-4 rounded">
                <span class="text-sm">Test mode (sandbox). Uncheck to go live.</span>
            </label>
        </div>

        <div class="shadcn-card rounded-xl border bg-card border-card-border text-card-foreground shadow-sm p-6 mb-6">
            <h2 class="text-lg font-semibold mb-4">URLs to register at {{ $gateway->name }}</h2>
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium mb-2">Return URL <span class="text-muted-foreground font-normal">(customer comes back here after paying)</span></label>
                    <input type="text" readonly value="{{ route('payment.callback', ['gateway' => $gateway->slug]) }}" onclick="this.select()"
                        class="flex h-10 w-full rounded-md border border-input bg-muted px-3 py-2 text-sm font-mono">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-2">Webhook URL <span class="text-muted-foreground font-normal">(for Paddle, Lemon Squeezy, Polar)</span></label>
                    <input type="text" readonly value="{{ route('payment.webhook', ['gateway' => $gateway->slug]) }}" onclick="this.select()"
                        class="flex h-10 w-full rounded-md border border-input bg-muted px-3 py-2 text-sm font-mono">
                </div>
            </div>
        </div>

        <div class="flex items-center gap-3">
            <button type="submit" class="inline-flex items-center justify-center rounded-md text-sm font-medium h-10 px-6 bg-primary text-primary-foreground hover-elevate">Save settings</button>
            <a href="{{ route('admin.payment-gateways.index') }}" class="inline-flex items-center justify-center rounded-md text-sm font-medium h-10 px-6 border border-input bg-background hover-elevate">Cancel</a>
        </div>
    </form>
</div>
@endsection
