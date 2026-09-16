@extends('layouts.admin')

@section('title', 'Transactions')

@section('content')
<div class="p-6 md:p-8">
    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-3xl font-bold text-foreground mb-2">
                Transactions
            </h1>
            <p class="text-muted-foreground">
                Manage payment transactions
            </p>
        </div>
        <a href="{{ route('admin.transactions.create') }}">
            <button class="inline-flex items-center justify-center gap-2 whitespace-nowrap rounded-md text-sm font-medium min-h-10 px-4 py-2 bg-primary text-primary-foreground border border-primary-border hover-elevate active-elevate-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                New Transaction
            </button>
        </a>
    </div>

    <div class="shadcn-card rounded-xl border bg-card border-card-border text-card-foreground shadow-sm p-6">
        @if($transactions->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b">
                            <th class="text-left p-3 font-semibold">Transaction ID</th>
                            <th class="text-left p-3 font-semibold">User</th>
                            <th class="text-left p-3 font-semibold">Order</th>
                            <th class="text-left p-3 font-semibold">Type</th>
                            <th class="text-left p-3 font-semibold">Amount</th>
                            <th class="text-left p-3 font-semibold">Status</th>
                            <th class="text-left p-3 font-semibold">Payment Method</th>
                            <th class="text-left p-3 font-semibold">Date</th>
                            <th class="text-right p-3 font-semibold">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($transactions as $transaction)
                        <tr class="border-b hover:bg-muted/50">
                            <td class="p-3">
                                <span class="font-mono text-sm">{{ $transaction->transaction_id }}</span>
                            </td>
                            <td class="p-3">{{ $transaction->user->name ?? 'N/A' }}</td>
                            <td class="p-3 text-sm text-muted-foreground">
                                @if($transaction->order)
                                    {{ $transaction->order->order_number }}
                                @else
                                    —
                                @endif
                            </td>
                            <td class="p-3">
                                <span class="px-2 py-1 rounded text-xs font-medium
                                    @if($transaction->type === 'payment') bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200
                                    @elseif($transaction->type === 'refund') bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200
                                    @else bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200
                                    @endif capitalize">
                                    {{ $transaction->type }}
                                </span>
                            </td>
                            <td class="p-3">
                                <span class="font-medium">${{ number_format($transaction->amount, 2) }}</span>
                                <span class="text-xs text-muted-foreground">{{ $transaction->currency }}</span>
                            </td>
                            <td class="p-3">
                                <span class="px-2 py-1 rounded text-xs font-medium
                                    @if($transaction->status === 'completed') bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200
                                    @elseif($transaction->status === 'failed') bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200
                                    @elseif($transaction->status === 'cancelled') bg-gray-100 text-gray-800 dark:bg-gray-800 dark:text-gray-200
                                    @else bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200
                                    @endif capitalize">
                                    {{ $transaction->status }}
                                </span>
                            </td>
                            <td class="p-3 text-sm text-muted-foreground">{{ $transaction->payment_method }}</td>
                            <td class="p-3 text-sm text-muted-foreground">{{ $transaction->created_at->format('d/m/Y H:i') }}</td>
                            <td class="p-3">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('admin.transactions.edit', $transaction->id) }}">
                                        <button class="inline-flex items-center justify-center gap-2 whitespace-nowrap rounded-md text-sm font-medium min-h-9 w-9 border border-input bg-background text-foreground hover-elevate active-elevate-2">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                            </svg>
                                        </button>
                                    </a>
                                    <form method="POST" action="{{ route('admin.transactions.destroy', $transaction->id) }}" onsubmit="return confirm('Are you sure? This action cannot be undone.');" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="inline-flex items-center justify-center gap-2 whitespace-nowrap rounded-md text-sm font-medium min-h-9 w-9 border border-input bg-background text-foreground hover-elevate active-elevate-2">
                                            <svg class="w-4 h-4 text-destructive" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-center py-12">
                <p class="text-muted-foreground mb-4">
                    No transactions yet
                </p>
                <a href="{{ route('admin.transactions.create') }}">
                    <button class="inline-flex items-center justify-center gap-2 whitespace-nowrap rounded-md text-sm font-medium min-h-10 px-4 py-2 bg-primary text-primary-foreground border border-primary-border hover-elevate active-elevate-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                        Create First Transaction
                    </button>
                </a>
            </div>
        @endif
    </div>
</div>
@endsection
