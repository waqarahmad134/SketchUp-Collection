@extends('layouts.admin')

@section('title', 'Edit Setting')

@section('content')
<div class="p-6 md:p-8">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-foreground mb-2">
            Edit Setting
        </h1>
        <p class="text-muted-foreground">
            Update setting value
        </p>
    </div>

    <div class="shadcn-card rounded-xl border bg-card border-card-border text-card-foreground shadow-sm p-6 max-w-2xl">
        <form method="POST" action="{{ route('admin.settings.update', $setting->key) }}">
            @csrf
            @method('PUT')
            
            <div class="space-y-6">
                <div>
                    <label for="key" class="block text-sm font-medium mb-2">Key</label>
                    <input
                        type="text"
                        id="key"
                        name="key"
                        value="{{ $setting->key }}"
                        disabled
                        class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background opacity-50 cursor-not-allowed font-mono"
                    >
                    <p class="mt-1 text-xs text-muted-foreground">Setting key cannot be changed</p>
                </div>

                <div>
                    <label for="value" class="block text-sm font-medium mb-2">Value</label>
                    <textarea
                        id="value"
                        name="value"
                        rows="5"
                        class="flex w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring"
                        placeholder="Setting value"
                    >{{ old('value', $setting->value) }}</textarea>
                    @error('value')
                        <p class="mt-1 text-sm text-destructive">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex gap-3 pt-4">
                    <button
                        type="submit"
                        class="inline-flex items-center justify-center gap-2 whitespace-nowrap rounded-md text-sm font-medium min-h-10 px-4 py-2 bg-primary text-primary-foreground border border-primary-border hover-elevate active-elevate-2"
                    >
                        Update Setting
                    </button>
                    <a href="{{ route('admin.settings.index') }}">
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
