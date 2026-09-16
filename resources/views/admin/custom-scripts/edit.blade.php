@extends('layouts.admin')

@section('title', 'Edit Custom Script')

@section('content')
<div class="p-6 md:p-8">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-foreground mb-2">
            Edit Custom Script
        </h1>
        <p class="text-muted-foreground">
            Update custom script
        </p>
    </div>

    <div class="shadcn-card rounded-xl border bg-card border-card-border text-card-foreground shadow-sm p-6 max-w-3xl">
        <form method="POST" action="{{ route('admin.custom-scripts.update', $script->id) }}">
            @csrf
            @method('PUT')
            
            <div class="space-y-6">
                <div>
                    <label for="name" class="block text-sm font-medium mb-2">Script Name *</label>
                    <input
                        type="text"
                        id="name"
                        name="name"
                        value="{{ old('name', $script->name) }}"
                        required
                        class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring"
                        placeholder="e.g., Google Analytics, Facebook Pixel"
                    >
                    @error('name')
                        <p class="mt-1 text-sm text-destructive">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="position" class="block text-sm font-medium mb-2">Position *</label>
                    <select
                        id="position"
                        name="position"
                        required
                        class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring"
                    >
                        <option value="head" {{ old('position', $script->position) === 'head' ? 'selected' : '' }}>Head (Before &lt;/head&gt;)</option>
                        <option value="body_start" {{ old('position', $script->position) === 'body_start' ? 'selected' : '' }}>Body Start (After &lt;body&gt;)</option>
                        <option value="body_end" {{ old('position', $script->position) === 'body_end' ? 'selected' : '' }}>Body End (Before &lt;/body&gt;)</option>
                    </select>
                    <p class="mt-1 text-xs text-muted-foreground">Where should this script be inserted?</p>
                    @error('position')
                        <p class="mt-1 text-sm text-destructive">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="code" class="block text-sm font-medium mb-2">Script Code *</label>
                    <textarea
                        id="code"
                        name="code"
                        rows="12"
                        required
                        class="flex w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring font-mono"
                        placeholder="&lt;script&gt;&#10;  // Your script code here&#10;&lt;/script&gt;"
                    >{{ old('code', $script->code) }}</textarea>
                    <p class="mt-1 text-xs text-muted-foreground">Include the &lt;script&gt; tags or any HTML/CSS code</p>
                    @error('code')
                        <p class="mt-1 text-sm text-destructive">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="sort_order" class="block text-sm font-medium mb-2">Sort Order</label>
                        <input
                            type="number"
                            id="sort_order"
                            name="sort_order"
                            value="{{ old('sort_order', $script->sort_order) }}"
                            class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring"
                        >
                        <p class="mt-1 text-xs text-muted-foreground">Lower numbers load first</p>
                    </div>

                    <div class="flex items-center gap-2 pt-8">
                        <input
                            type="checkbox"
                            id="is_active"
                            name="is_active"
                            value="1"
                            {{ old('is_active', $script->is_active) ? 'checked' : '' }}
                            class="w-4 h-4 rounded border-input"
                        >
                        <label for="is_active" class="text-sm font-medium">Active</label>
                        <p class="text-xs text-muted-foreground ml-auto">Only active scripts will be loaded</p>
                    </div>
                </div>

                <div class="flex gap-3 pt-4">
                    <button
                        type="submit"
                        class="inline-flex items-center justify-center gap-2 whitespace-nowrap rounded-md text-sm font-medium min-h-10 px-4 py-2 bg-primary text-primary-foreground border border-primary-border hover-elevate active-elevate-2"
                    >
                        Update Script
                    </button>
                    <a href="{{ route('admin.custom-scripts.index') }}">
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
