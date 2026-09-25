@extends('layouts.admin')

@section('title', $redirect->exists ? 'Edit Redirect' : 'New Redirect')

@section('content')
<div class="p-6 md:p-8">
    <div class="max-w-2xl">
        <h1 class="text-3xl font-bold text-foreground mb-8">{{ $redirect->exists ? 'Edit Redirect' : 'New Redirect' }}</h1>

        <div class="shadcn-card rounded-xl border bg-card border-card-border text-card-foreground shadow-sm p-6">
            <form method="POST" action="{{ $redirect->exists ? route('admin.redirects.update', $redirect) : route('admin.redirects.store') }}" class="space-y-5">
                @csrf
                @if($redirect->exists)
                    @method('PUT')
                @endif

                <div>
                    <label for="from_path" class="block text-sm font-medium mb-2">From path *</label>
                    <input type="text" id="from_path" name="from_path" required
                        value="{{ old('from_path', $redirect->from_path) }}"
                        placeholder="/old-bundle-url"
                        class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm font-mono">
                    <p class="mt-1 text-xs text-muted-foreground">The old URL path, e.g. /bundles/old-slug</p>
                    @error('from_path')<p class="mt-1 text-sm text-destructive">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="to_path" class="block text-sm font-medium mb-2">To path *</label>
                    <input type="text" id="to_path" name="to_path" required
                        value="{{ old('to_path', $redirect->to_path) }}"
                        placeholder="/bundles/new-slug"
                        class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm font-mono">
                    @error('to_path')<p class="mt-1 text-sm text-destructive">{{ $message }}</p>@enderror
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="status_code" class="block text-sm font-medium mb-2">Type</label>
                        <select id="status_code" name="status_code" class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm">
                            <option value="301" {{ old('status_code', $redirect->status_code ?? 301) == 301 ? 'selected' : '' }}>301 Permanent</option>
                            <option value="302" {{ old('status_code', $redirect->status_code ?? 301) == 302 ? 'selected' : '' }}>302 Temporary</option>
                        </select>
                    </div>
                    <div class="flex items-end pb-2">
                        <label class="flex items-center gap-2 text-sm">
                            <input type="checkbox" name="is_active" value="1" {{ old('is_active', $redirect->is_active ?? true) ? 'checked' : '' }} class="rounded">
                            Active
                        </label>
                    </div>
                </div>

                <div class="flex gap-3">
                    <button type="submit" class="rounded-md text-sm font-medium min-h-10 px-6 py-2 bg-primary text-primary-foreground border border-primary-border hover-elevate">
                        {{ $redirect->exists ? 'Update' : 'Create' }} Redirect
                    </button>
                    <a href="{{ route('admin.redirects.index') }}" class="rounded-md text-sm font-medium min-h-10 px-6 py-2 border border-input hover-elevate inline-flex items-center">
                        Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
