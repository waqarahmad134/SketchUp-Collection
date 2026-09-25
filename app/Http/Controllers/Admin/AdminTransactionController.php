<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;

class AdminTransactionController extends Controller
{
    public function index()
    {
        $transactions = Transaction::with(['user', 'order'])->latest()->get();
        return view('admin.transactions.index', compact('transactions'));
    }

    public function create()
    {
        $users = User::all();
        $orders = Order::all();
        return view('admin.transactions.create', compact('users', 'orders'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'order_id' => 'nullable|exists:orders,id',
            'type' => 'required|in:payment,refund,payout',
            'status' => 'required|in:pending,completed,failed,cancelled',
            'amount' => 'required|numeric|min:0',
            'currency' => 'nullable|string|max:3',
            'payment_method' => 'required|string|max:255',
            'payment_gateway' => 'nullable|string|max:255',
            'gateway_transaction_id' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'completed_at' => 'nullable|date',
        ]);

        $validated['currency'] = $validated['currency'] ?? 'USD';

        Transaction::create($validated);

        return redirect()->route('admin.transactions.index')->with('success', 'Transaction created successfully');
    }

    public function edit(string $id)
    {
        $transaction = Transaction::findOrFail($id);
        $users = User::all();
        $orders = Order::all();
        return view('admin.transactions.edit', compact('transaction', 'users', 'orders'));
    }

    public function update(Request $request, string $id)
    {
        $transaction = Transaction::findOrFail($id);

        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'order_id' => 'nullable|exists:orders,id',
            'type' => 'required|in:payment,refund,payout',
            'status' => 'required|in:pending,completed,failed,cancelled',
            'amount' => 'required|numeric|min:0',
            'currency' => 'nullable|string|max:3',
            'payment_method' => 'required|string|max:255',
            'payment_gateway' => 'nullable|string|max:255',
            'gateway_transaction_id' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'completed_at' => 'nullable|date',
        ]);

        $validated['currency'] = $validated['currency'] ?? 'USD';

        $transaction->update($validated);

        return redirect()->route('admin.transactions.index')->with('success', 'Transaction updated successfully');
    }

    public function destroy(string $id)
    {
        $transaction = Transaction::findOrFail($id);
        $transaction->delete();

        return redirect()->route('admin.transactions.index')->with('success', 'Transaction deleted successfully');
    }
}
