@extends('layouts.admin')

@section('title', 'Settings')

@section('content')
<div class="p-6 md:p-8">
    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-3xl font-bold text-foreground mb-2">
                Settings
            </h1>
            <p class="text-muted-foreground">
                Manage application settings
            </p>
        </div>
        <button
            onclick="document.getElementById('new-setting-form').classList.toggle('hidden')"
            class="inline-flex items-center justify-center gap-2 whitespace-nowrap rounded-md text-sm font-medium min-h-10 px-4 py-2 bg-primary text-primary-foreground border border-primary-border hover-elevate active-elevate-2"
        >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            New Setting
        </button>
    </div>

    <div class="space-y-6">
        <!-- New Setting Form -->
        <div id="new-setting-form" class="hidden shadcn-card rounded-xl border bg-card border-card-border text-card-foreground shadow-sm p-6">
            <h2 class="text-xl font-semibold mb-4">Add New Setting</h2>
            <form method="POST" action="{{ route('admin.settings.store') }}">
                @csrf
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="key" class="block text-sm font-medium mb-2">Key *</label>
                        <input
                            type="text"
                            id="key"
                            name="key"
                            value="{{ old('key') }}"
                            required
                            class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring"
                            placeholder="setting_key"
                        >
                        @error('key')
                            <p class="mt-1 text-sm text-destructive">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="value" class="block text-sm font-medium mb-2">Value</label>
                        <input
                            type="text"
                            id="value"
                            name="value"
                            value="{{ old('value') }}"
                            class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring"
                            placeholder="Setting value"
                        >
                    </div>
                </div>
                <div class="flex gap-3 mt-4">
                    <button type="submit" class="inline-flex items-center justify-center gap-2 whitespace-nowrap rounded-md text-sm font-medium min-h-10 px-4 py-2 bg-primary text-primary-foreground border border-primary-border hover-elevate active-elevate-2">
                        Create Setting
                    </button>
                    <button type="button" onclick="document.getElementById('new-setting-form').classList.add('hidden')" class="inline-flex items-center justify-center gap-2 whitespace-nowrap rounded-md text-sm font-medium min-h-10 px-4 py-2 border border-input bg-background text-foreground hover-elevate active-elevate-2">
                        Cancel
                    </button>
                </div>
            </form>
        </div>

        <!-- Settings List -->
        <div class="shadcn-card rounded-xl border bg-card border-card-border text-card-foreground shadow-sm p-6">
            @if($settings->count() > 0)
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="border-b">
                                <th class="text-left p-3 font-semibold">Key</th>
                                <th class="text-left p-3 font-semibold">Value</th>
                                <th class="text-left p-3 font-semibold">Updated</th>
                                <th class="text-right p-3 font-semibold">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($settings as $setting)
                            <tr class="border-b hover:bg-muted/50">
                                <td class="p-3">
                                    <span class="font-medium font-mono">{{ $setting->key }}</span>
                                </td>
                                <td class="p-3">
                                    <span class="text-sm text-muted-foreground">
                                        {{ \Illuminate\Support\Str::limit($setting->value, 100) }}
                                    </span>
                                </td>
                                <td class="p-3 text-sm text-muted-foreground">
                                    {{ $setting->updated_at->format('d/m/Y H:i') }}
                                </td>
                                <td class="p-3">
                                    <div class="flex items-center justify-end gap-2">
                                        <a href="{{ route('admin.settings.edit', $setting->key) }}">
                                            <button class="inline-flex items-center justify-center gap-2 whitespace-nowrap rounded-md text-sm font-medium min-h-9 w-9 border border-input bg-background text-foreground hover-elevate active-elevate-2">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                </svg>
                                            </button>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-12">
                    <p class="text-muted-foreground mb-4">
                        No settings yet
                    </p>
                    <button
                        onclick="document.getElementById('new-setting-form').classList.toggle('hidden')"
                        class="inline-flex items-center justify-center gap-2 whitespace-nowrap rounded-md text-sm font-medium min-h-10 px-4 py-2 bg-primary text-primary-foreground border border-primary-border hover-elevate active-elevate-2"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                        Create First Setting
                    </button>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
