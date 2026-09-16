@extends('layouts.app')

@section('content')
    <section class="py-24">
        <div class="container mx-auto px-4">
            <div class="max-w-3xl mx-auto prose-content">
                <h1 class="text-4xl md:text-5xl font-bold font-display mb-8">Refund <span class="gradient-text">Policy</span></h1>

                <div class="space-y-6 text-muted-foreground">
                    <p>Because our products are digital downloads delivered instantly, <strong class="text-foreground">all sales are final</strong> and we do not offer refunds for change of mind, accidental purchases, or compatibility issues you could have checked before buying. Please review the bundle contents, file formats and SketchUp version on the product page before completing your purchase.</p>

                    <h2 class="text-2xl font-bold text-foreground font-display">When we will refund or replace</h2>
                    <ul class="list-disc pl-6 space-y-2">
                        <li>Files are corrupted, incomplete, or fail to download after we have tried to help you.</li>
                        <li>The delivered files do not match the description on the product page.</li>
                        <li>You were charged twice for the same order.</li>
                    </ul>
                    <p>Contact us within <strong class="text-foreground">7 days of purchase</strong> with your order number. We will first try to fix the problem by replacing the files. If we cannot resolve it, we will issue a full refund to your original payment method.</p>

                    <h2 class="text-2xl font-bold text-foreground font-display">How refunds are processed</h2>
                    <p>Approved refunds are sent back to the original payment method within 5 to 10 business days, depending on your bank or wallet provider. SKP coins used on the order are returned to your coin balance instead of cash.</p>

                    <h2 class="text-2xl font-bold text-foreground font-display">Contact</h2>
                    <p>For any refund request, reach us through our <a href="{{ route('contact') }}" class="text-cyan-400 hover:underline">contact page</a> with your order number and a short description of the issue.</p>

                    <p class="text-sm">Last updated: {{ now()->format('F Y') }}</p>
                </div>
            </div>
        </div>
    </section>
@endsection
