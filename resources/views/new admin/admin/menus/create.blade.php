@extends('layouts.admin')

@section('title', 'New Menu Item')

@section('content')
<div class="p-6 md:p-8">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-foreground mb-2">
            New Menu Item
        </h1>
        <p class="text-muted-foreground">
            Add a new navigation menu item
        </p>
    </div>

    <div class="shadcn-card rounded-xl border bg-card border-card-border text-card-foreground shadow-sm p-6 max-w-2xl">
        <form method="POST" action="{{ route('newadmin.menus.store') }}">
            @csrf
            
            <div class="space-y-6">
                <div>
                    <label for="label" class="block text-sm font-medium mb-2">Label *</label>
                    <input
                        type="text"
                        id="label"
                        name="label"
                        value="{{ old('label') }}"
                        required
                        class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring"
                        placeholder="e.g., Home, Products, About"
                    >
                    @error('label')
                        <p class="mt-1 text-sm text-destructive">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="url" class="block text-sm font-medium mb-2">URL</label>
                    <input
                        type="text"
                        id="url"
                        name="url"
                        value="{{ old('url') }}"
                        class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring"
                        placeholder="e.g., / or https://example.com"
                    >
                    <p class="mt-1 text-xs text-muted-foreground">Leave empty if using route</p>
                    @error('url')
                        <p class="mt-1 text-sm text-destructive">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="route" class="block text-sm font-medium mb-2">Route</label>
                    <input
                        type="text"
                        id="route"
                        name="route"
                        value="{{ old('route') }}"
                        class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring"
                        placeholder="e.g., products.index"
                    >
                    <p class="mt-1 text-xs text-muted-foreground">Laravel route name. Leave empty if using URL</p>
                    @error('route')
                        <p class="mt-1 text-sm text-destructive">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="target" class="block text-sm font-medium mb-2">Target</label>
                        <select
                            id="target"
                            name="target"
                            class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring"
                        >
                            <option value="_self" {{ old('target', '_self') === '_self' ? 'selected' : '' }}>Same Window</option>
                            <option value="_blank" {{ old('target') === '_blank' ? 'selected' : '' }}>New Window</option>
                        </select>
                    </div>

                    <div>
                        <label for="sort_order" class="block text-sm font-medium mb-2">Sort Order</label>
                        <input
                            type="number"
                            id="sort_order"
                            name="sort_order"
                            value="{{ old('sort_order', 0) }}"
                            class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring"
                        >
                    </div>
                </div>

                <div>
                    <label for="icon" class="block text-sm font-medium mb-2">Icon</label>
                    <input
                        type="text"
                        id="icon"
                        name="icon"
                        value="{{ old('icon') }}"
                        class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring"
                        placeholder="heroicon-o-home"
                    >
                    <p class="mt-1 text-xs text-muted-foreground">Heroicon name (optional)</p>
                </div>

                <div>
                    <label for="css_class" class="block text-sm font-medium mb-2">CSS Class</label>
                    <input
                        type="text"
                        id="css_class"
                        name="css_class"
                        value="{{ old('css_class') }}"
                        class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring"
                        placeholder="custom-class"
                    >
                </div>

                <div class="flex items-center gap-2">
                    <input
                        type="checkbox"
                        id="is_active"
                        name="is_active"
                        value="1"
                        {{ old('is_active', true) ? 'checked' : '' }}
                        class="w-4 h-4 rounded border-input"
                    >
                    <label for="is_active" class="text-sm font-medium">Active</label>
                </div>

                <div class="flex gap-3 pt-4">
                    <button
                        type="submit"
                        class="inline-flex items-center justify-center gap-2 whitespace-nowrap rounded-md text-sm font-medium min-h-10 px-4 py-2 bg-primary text-primary-foreground border border-primary-border hover-elevate active-elevate-2"
                    >
                        Create Menu Item
                    </button>
                    <a href="{{ route('newadmin.menus.index') }}">
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
