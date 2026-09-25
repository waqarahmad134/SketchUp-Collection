@extends('layouts.admin')

@section('title', 'New Transaction')

@section('content')
<div class="p-6 md:p-8">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-foreground mb-2">
            New Transaction
        </h1>
        <p class="text-muted-foreground">
            Create a new transaction
        </p>
    </div>

    <div class="shadcn-card rounded-xl border bg-card border-card-border text-card-foreground shadow-sm p-6 max-w-3xl">
        <form method="POST" action="{{ route('admin.transactions.store') }}">
            @csrf
            
            <div class="space-y-6">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="user_id" class="block text-sm font-medium mb-2">User *</label>
                        <select
                            id="user_id"
                            name="user_id"
                            required
                            class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring"
                        >
                            <option value="">Select user</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}" {{ old('user_id') == $user->id ? 'selected' : '' }}>{{ $user->name }} ({{ $user->email }})</option>
                            @endforeach
                        </select>
                        @error('user_id')
                            <p class="mt-1 text-sm text-destructive">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="order_id" class="block text-sm font-medium mb-2">Order</label>
                        <select
                            id="order_id"
                            name="order_id"
                            class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring"
                        >
                            <option value="">Select order (optional)</option>
                            @foreach($orders as $order)
                                <option value="{{ $order->id }}" {{ old('order_id') == $order->id ? 'selected' : '' }}>{{ $order->order_number }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="type" class="block text-sm font-medium mb-2">Type *</label>
                        <select
                            id="type"
                            name="type"
                            required
                            class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring"
                        >
                            <option value="payment" {{ old('type', 'payment') === 'payment' ? 'selected' : '' }}>Payment</option>
                            <option value="refund" {{ old('type') === 'refund' ? 'selected' : '' }}>Refund</option>
                            <option value="payout" {{ old('type') === 'payout' ? 'selected' : '' }}>Payout</option>
                        </select>
                        @error('type')
                            <p class="mt-1 text-sm text-destructive">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="status" class="block text-sm font-medium mb-2">Status *</label>
                        <select
                            id="status"
                            name="status"
                            required
                            class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring"
                        >
                            <option value="pending" {{ old('status', 'pending') === 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="completed" {{ old('status') === 'completed' ? 'selected' : '' }}>Completed</option>
                            <option value="failed" {{ old('status') === 'failed' ? 'selected' : '' }}>Failed</option>
                            <option value="cancelled" {{ old('status') === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                        </select>
                        @error('status')
                            <p class="mt-1 text-sm text-destructive">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="amount" class="block text-sm font-medium mb-2">Amount *</label>
                        <input
                            type="number"
                            id="amount"
                            name="amount"
                            step="0.01"
                            min="0"
                            value="{{ old('amount') }}"
                            required
                            class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring"
                            placeholder="0.00"
                        >
                        @error('amount')
                            <p class="mt-1 text-sm text-destructive">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="currency" class="block text-sm font-medium mb-2">Currency</label>
                        <input
                            type="text"
                            id="currency"
                            name="currency"
                            value="{{ old('currency', 'USD') }}"
                            maxlength="3"
                            class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring"
                            placeholder="USD"
                        >
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="payment_method" class="block text-sm font-medium mb-2">Payment Method *</label>
                        <input
                            type="text"
                            id="payment_method"
                            name="payment_method"
                            value="{{ old('payment_method') }}"
                            required
                            class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring"
                            placeholder="Credit Card, PayPal, etc"
                        >
                        @error('payment_method')
                            <p class="mt-1 text-sm text-destructive">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="payment_gateway" class="block text-sm font-medium mb-2">Payment Gateway</label>
                        <input
                            type="text"
                            id="payment_gateway"
                            name="payment_gateway"
                            value="{{ old('payment_gateway') }}"
                            class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring"
                            placeholder="stripe, paypal, etc"
                        >
                    </div>
                </div>

                <div>
                    <label for="gateway_transaction_id" class="block text-sm font-medium mb-2">Gateway Transaction ID</label>
                    <input
                        type="text"
                        id="gateway_transaction_id"
                        name="gateway_transaction_id"
                        value="{{ old('gateway_transaction_id') }}"
                        class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring"
                        placeholder="Gateway transaction ID"
                    >
                </div>

                <div>
                    <label for="description" class="block text-sm font-medium mb-2">Description</label>
                    <textarea
                        id="description"
                        name="description"
                        rows="3"
                        class="flex w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring"
                        placeholder="Transaction description"
                    >{{ old('description') }}</textarea>
                </div>

                <div>
                    <label for="completed_at" class="block text-sm font-medium mb-2">Completed At</label>
                    <input
                        type="datetime-local"
                        id="completed_at"
                        name="completed_at"
                        value="{{ old('completed_at') }}"
                        class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring"
                    >
                </div>

                <div class="flex gap-3 pt-4">
                    <button
                        type="submit"
                        class="inline-flex items-center justify-center gap-2 whitespace-nowrap rounded-md text-sm font-medium min-h-10 px-4 py-2 bg-primary text-primary-foreground border border-primary-border hover-elevate active-elevate-2"
                    >
                        Create Transaction
                    </button>
                    <a href="{{ route('admin.transactions.index') }}">
                        <button
                            type="button"
                            class="inline-flex items-center justify-center gap-2 whitespace-nowrap rounded-md text-sm font-medium min-h-10 px-4 py-2 border border-input bg-background text-foreground hover-elevate active-elevate-2"
                        >
                            Cancel
                        </button>
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
