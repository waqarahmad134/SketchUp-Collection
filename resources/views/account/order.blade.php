@extends('layouts.app')

@section('content')
    <section class="py-16">
        <div class="container mx-auto px-4">
            <div class="max-w-4xl mx-auto">
                <a href="{{ route('account.dashboard') }}" class="inline-flex items-center gap-1 text-sm text-muted-foreground hover:text-cyan-400 mb-6">
                    <i data-lucide="arrow-left" class="w-4 h-4"></i> Back to my orders
                </a>

                <h1 class="text-3xl font-bold font-display mb-2">Order <span class="gradient-text">{{ $order->order_number }}</span></h1>
                <p class="text-muted-foreground mb-8">
                    Placed {{ $order->created_at->format('M d, Y h:i A') }} - Paid via {{ ucfirst($order->payment_method ?? 'card') }}
                </p>

                <div class="space-y-4 mb-8">
                    @foreach($order->items as $item)
                        <div class="glass-card rounded-2xl p-5 flex flex-wrap items-center gap-4">
                            @if($item->product_image)
                                <img src="{{ $item->product_image }}" alt="{{ $item->product_name }}" class="w-20 h-20 rounded-xl object-cover">
                            @endif
                            <div class="flex-1 min-w-[200px]">
                                <p class="font-semibold">{{ $item->product_name }}</p>
                                <p class="text-sm text-muted-foreground">
                                    Qty: {{ $item->quantity }} - ${{ number_format($item->total, 2) }}
                                    @if($item->product?->file_size)
                                        <span class="mx-1">•</span> {{ $item->product->file_size }}
                                    @endif
                                    @if($item->product?->sketchup_version)
                                        <span class="mx-1">•</span> SketchUp {{ $item->product->sketchup_version }}
                                    @endif
                                </p>
                            </div>
                            @if($item->product_slug)
                                <a href="{{ route('bundles.download', $item->product_slug) }}" class="inline-flex items-center gap-2 px-6 py-3 rounded-xl bg-gradient-to-r from-cyan-500 to-violet-500 text-background font-semibold shadow-lg hover:shadow-xl transition">
                                    <i data-lucide="download" class="w-4 h-4"></i> Download
                                </a>
                            @endif
                        </div>
                    @endforeach
                </div>

                <div class="glass-card rounded-2xl p-6 max-w-sm ml-auto">
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between">
                            <span class="text-muted-foreground">Subtotal</span>
                            <span>${{ number_format($order->subtotal, 2) }}</span>
                        </div>
                        @if($order->discount > 0)
                            <div class="flex justify-between text-green-400">
                                <span>Discount</span>
                                <span>-${{ number_format($order->discount, 2) }}</span>
                            </div>
                        @endif
                        <div class="flex justify-between font-bold text-lg pt-2 border-t border-border">
                            <span>Total paid</span>
                            <span class="gradient-text">${{ number_format($order->total, 2) }} {{ $order->currency }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
