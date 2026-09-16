<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: Arial, sans-serif; color: #1a1a2e; line-height: 1.6; margin: 0; padding: 0; }
        .wrapper { max-width: 600px; margin: 0 auto; padding: 24px; }
        .header { background: linear-gradient(135deg, #06b6d4, #8b5cf6); color: #fff; padding: 24px; border-radius: 12px 12px 0 0; }
        .body { background: #f8fafc; padding: 24px; border-radius: 0 0 12px 12px; }
        .item { background: #fff; border: 1px solid #e2e8f0; border-radius: 8px; padding: 12px 16px; margin-bottom: 12px; }
        .btn { display: inline-block; background: #06b6d4; color: #fff !important; text-decoration: none; padding: 10px 20px; border-radius: 8px; font-weight: bold; margin-top: 8px; }
        .totals { margin-top: 16px; }
        .muted { color: #64748b; font-size: 13px; }
    </style>
</head>
<body>
<div class="wrapper">
    <div class="header">
        <h2 style="margin:0;">Thank you for your order!</h2>
        <p style="margin:8px 0 0;">Order {{ $order->order_number }}</p>
    </div>
    <div class="body">
        <p>Hi {{ $order->customer_name ?? 'there' }},</p>
        <p>Your payment was successful. Your download links are below and also available anytime in your account.</p>

        @foreach($order->items as $item)
            <div class="item">
                <strong>{{ $item->product_name }}</strong><br>
                <span class="muted">Qty: {{ $item->quantity }} - ${{ number_format($item->total, 2) }}</span><br>
                @if($item->product_slug)
                    <a class="btn" href="{{ route('bundles.download', $item->product_slug) }}">Download Now</a>
                @endif
            </div>
        @endforeach

        <div class="totals">
            <p class="muted">
                Subtotal: ${{ number_format($order->subtotal, 2) }}<br>
                @if($order->discount > 0)
                    Discount: -${{ number_format($order->discount, 2) }}<br>
                @endif
                <strong>Total paid: ${{ number_format($order->total, 2) }} {{ $order->currency }}</strong>
            </p>
        </div>

        <p>
            <a class="btn" href="{{ route('account.dashboard') }}">View My Orders</a>
        </p>

        <p class="muted">Need help? Reply to this email or contact us anytime.</p>
    </div>
</div>
</body>
</html>
