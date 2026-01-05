@extends('layouts.admin')

@section('title', 'Edit Coupon')

@section('content')
<div class="p-6 md:p-8">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-foreground mb-2">
            Edit Coupon
        </h1>
        <p class="text-muted-foreground">
            Update discount coupon
        </p>
    </div>

    <div class="shadcn-card rounded-xl border bg-card border-card-border text-card-foreground shadow-sm p-6 max-w-3xl">
        <form method="POST" action="{{ route('newadmin.coupons.update', $coupon->id) }}">
            @csrf
            @method('PUT')
            
            <div class="space-y-6">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="code" class="block text-sm font-medium mb-2">Coupon Code *</label>
                        <input
                            type="text"
                            id="code"
                            name="code"
                            value="{{ old('code', $coupon->code) }}"
                            required
                            maxlength="50"
                            class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring font-mono uppercase"
                            style="text-transform: uppercase;"
                        >
                        <p class="mt-1 text-xs text-muted-foreground">Will be converted to uppercase</p>
                        @error('code')
                            <p class="mt-1 text-sm text-destructive">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="type" class="block text-sm font-medium mb-2">Type *</label>
                        <select
                            id="type"
                            name="type"
                            required
                            class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring"
                        >
                            <option value="percentage" {{ old('type', $coupon->type) === 'percentage' ? 'selected' : '' }}>Percentage</option>
                            <option value="flat" {{ old('type', $coupon->type) === 'flat' ? 'selected' : '' }}>Flat Amount</option>
                        </select>
                        @error('type')
                            <p class="mt-1 text-sm text-destructive">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="value" class="block text-sm font-medium mb-2">Value *</label>
                        <input
                            type="number"
                            id="value"
                            name="value"
                            step="0.01"
                            min="0"
                            value="{{ old('value', $coupon->value) }}"
                            required
                            class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring"
                        >
                        <p class="mt-1 text-xs text-muted-foreground" id="value-help">
                            @if($coupon->type === 'percentage')
                                Percentage (e.g., 10 for 10%)
                            @else
                                Fixed amount in dollars (e.g., 20 for $20)
                            @endif
                        </p>
                        @error('value')
                            <p class="mt-1 text-sm text-destructive">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="min_order" class="block text-sm font-medium mb-2">Minimum Order</label>
                        <input
                            type="number"
                            id="min_order"
                            name="min_order"
                            step="0.01"
                            min="0"
                            value="{{ old('min_order', $coupon->min_order) }}"
                            class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring"
                        >
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4" id="max_discount_container" style="display: {{ $coupon->type === 'percentage' ? 'grid' : 'none' }};">
                    <div>
                        <label for="max_discount" class="block text-sm font-medium mb-2">Maximum Discount</label>
                        <input
                            type="number"
                            id="max_discount"
                            name="max_discount"
                            step="0.01"
                            min="0"
                            value="{{ old('max_discount', $coupon->max_discount) }}"
                            class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring"
                        >
                        <p class="mt-1 text-xs text-muted-foreground">For percentage coupons only</p>
                    </div>

                    <div>
                        <label for="usage_limit" class="block text-sm font-medium mb-2">Total Usage Limit</label>
                        <input
                            type="number"
                            id="usage_limit"
                            name="usage_limit"
                            min="1"
                            value="{{ old('usage_limit', $coupon->usage_limit) }}"
                            class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring"
                            placeholder="Unlimited"
                        >
                        <p class="mt-1 text-xs text-muted-foreground">Leave empty for unlimited</p>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="usage_per_user" class="block text-sm font-medium mb-2">Usage Per User</label>
                        <input
                            type="number"
                            id="usage_per_user"
                            name="usage_per_user"
                            min="1"
                            value="{{ old('usage_per_user', $coupon->usage_per_user) }}"
                            class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring"
                        >
                    </div>

                    <div>
                        <label for="expires_at" class="block text-sm font-medium mb-2">Expires At</label>
                        <input
                            type="datetime-local"
                            id="expires_at"
                            name="expires_at"
                            value="{{ old('expires_at', $coupon->expires_at ? $coupon->expires_at->format('Y-m-d\TH:i') : '') }}"
                            class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring"
                        >
                        <p class="mt-1 text-xs text-muted-foreground">Leave empty for no expiration</p>
                    </div>
                </div>

                <div>
                    <label for="description" class="block text-sm font-medium mb-2">Description</label>
                    <textarea
                        id="description"
                        name="description"
                        rows="3"
                        maxlength="500"
                        class="flex w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring"
                        placeholder="Coupon description"
                    >{{ old('description', $coupon->description) }}</textarea>
                </div>

                <div class="flex items-center gap-2">
                    <input
                        type="checkbox"
                        id="is_active"
                        name="is_active"
                        value="1"
                        {{ old('is_active', $coupon->is_active) ? 'checked' : '' }}
                        class="w-4 h-4 rounded border-input"
                    >
                    <label for="is_active" class="text-sm font-medium">Active</label>
                </div>

                <div class="flex gap-3 pt-4">
                    <button
                        type="submit"
                        class="inline-flex items-center justify-center gap-2 whitespace-nowrap rounded-md text-sm font-medium min-h-10 px-4 py-2 bg-primary text-primary-foreground border border-primary-border hover-elevate active-elevate-2"
                    >
                        Update Coupon
                    </button>
                    <a href="{{ route('newadmin.coupons.index') }}">
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

@push('scripts')
<script>
document.getElementById('type').addEventListener('change', function() {
    const type = this.value;
    const valueHelp = document.getElementById('value-help');
    const maxDiscountContainer = document.getElementById('max_discount_container');
    
    if (type === 'percentage') {
        valueHelp.textContent = 'Percentage (e.g., 10 for 10%)';
        maxDiscountContainer.style.display = 'grid';
    } else {
        valueHelp.textContent = 'Fixed amount in dollars (e.g., 20 for $20)';
        maxDiscountContainer.style.display = 'none';
    }
});
</script>
@endpush
@endsection
