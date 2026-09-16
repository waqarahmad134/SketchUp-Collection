<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\User;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AdminOrderController extends Controller
{
    public function index()
    {
        $orders = Order::with(['user', 'items'])->latest()->get();
        return view('admin.orders.index', compact('orders'));
    }

    public function create()
    {
        $users = User::all();
        $products = Product::where('is_active', true)->get();
        return view('admin.orders.create', compact('users', 'products'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'status' => 'required|in:pending,processing,completed,cancelled,refunded',
            'payment_status' => 'required|in:pending,paid,failed,refunded',
            'payment_method' => 'nullable|string|max:255',
            'customer_name' => 'required|string|max:255',
            'customer_email' => 'required|email|max:255',
            'customer_phone' => 'nullable|string|max:255',
            'customer_note' => 'nullable|string',
            'subtotal' => 'required|numeric|min:0',
            'tax' => 'nullable|numeric|min:0',
            'discount' => 'nullable|numeric|min:0',
            'total' => 'required|numeric|min:0',
            'currency' => 'nullable|string|max:3',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.price' => 'required|numeric|min:0',
        ]);

        $validated['order_number'] = 'ORD-' . strtoupper(Str::random(10));
        $validated['currency'] = $validated['currency'] ?? 'USD';
        $validated['tax'] = $validated['tax'] ?? 0;
        $validated['discount'] = $validated['discount'] ?? 0;

        $order = Order::create($validated);

        // Create order items
        foreach ($validated['items'] as $itemData) {
            $product = Product::find($itemData['product_id']);
            $order->items()->create([
                'product_id' => $product->id,
                'product_name' => $product->title,
                'product_slug' => $product->slug,
                'product_image' => $product->image,
                'quantity' => $itemData['quantity'],
                'price' => $itemData['price'],
                'total' => $itemData['quantity'] * $itemData['price'],
            ]);
        }

        return redirect()->route('admin.orders.index')->with('success', 'Order created successfully');
    }

    public function show(string $id)
    {
        $order = Order::with(['user', 'items.product'])->findOrFail($id);
        return view('admin.orders.show', compact('order'));
    }

    public function edit(string $id)
    {
        $order = Order::with(['items'])->findOrFail($id);
        $users = User::all();
        $products = Product::where('is_active', true)->get();
        return view('admin.orders.edit', compact('order', 'users', 'products'));
    }

    public function update(Request $request, string $id)
    {
        $order = Order::findOrFail($id);

        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'status' => 'required|in:pending,processing,completed,cancelled,refunded',
            'payment_status' => 'required|in:pending,paid,failed,refunded',
            'payment_method' => 'nullable|string|max:255',
            'customer_name' => 'required|string|max:255',
            'customer_email' => 'required|email|max:255',
            'customer_phone' => 'nullable|string|max:255',
            'customer_note' => 'nullable|string',
            'subtotal' => 'required|numeric|min:0',
            'tax' => 'nullable|numeric|min:0',
            'discount' => 'nullable|numeric|min:0',
            'total' => 'required|numeric|min:0',
            'currency' => 'nullable|string|max:3',
        ]);

        $validated['currency'] = $validated['currency'] ?? 'USD';
        $validated['tax'] = $validated['tax'] ?? 0;
        $validated['discount'] = $validated['discount'] ?? 0;

        $order->update($validated);

        return redirect()->route('admin.orders.index')->with('success', 'Order updated successfully');
    }

    public function destroy(string $id)
    {
        $order = Order::findOrFail($id);
        $order->items()->delete();
        $order->delete();

        return redirect()->route('admin.orders.index')->with('success', 'Order deleted successfully');
    }
}
