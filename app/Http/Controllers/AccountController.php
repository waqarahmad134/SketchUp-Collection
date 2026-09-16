<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AccountController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Customer account dashboard: order history.
     */
    public function dashboard(Request $request): View
    {
        $orders = Order::where('user_id', $request->user()->id)
            ->with('items')
            ->latest()
            ->paginate(10);

        return view('account.dashboard', [
            'title' => 'My Account - SketchUp Collection',
            'metaDescription' => 'View your orders and download your purchased bundles.',
            'orders' => $orders,
            'user' => $request->user(),
        ]);
    }

    /**
     * Single order with download links for each purchased item.
     */
    public function order(Request $request, Order $order): View
    {
        abort_unless($order->user_id === $request->user()->id, 403);

        $order->load('items');

        return view('account.order', [
            'title' => 'Order ' . $order->order_number . ' - SketchUp Collection',
            'metaDescription' => 'Order details and downloads.',
            'order' => $order,
        ]);
    }
}
