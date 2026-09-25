@extends('layouts.admin')

@section('title', 'Menus')

@section('content')
<div class="p-6 md:p-8">
    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-3xl font-bold text-foreground mb-2">
                Menus
            </h1>
            <p class="text-muted-foreground">
                Manage navigation menu items
            </p>
        </div>
        <a href="{{ route('admin.menus.create') }}">
            <button class="inline-flex items-center justify-center gap-2 whitespace-nowrap rounded-md text-sm font-medium min-h-10 px-4 py-2 bg-primary text-primary-foreground border border-primary-border hover-elevate active-elevate-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                New Menu Item
            </button>
        </a>
    </div>

    <div class="shadcn-card rounded-xl border bg-card border-card-border text-card-foreground shadow-sm p-6">
        @if($menus->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b">
                            <th class="text-left p-3 font-semibold">Label</th>
                            <th class="text-left p-3 font-semibold">URL/Route</th>
                            <th class="text-left p-3 font-semibold">Target</th>
                            <th class="text-left p-3 font-semibold">Sort Order</th>
                            <th class="text-left p-3 font-semibold">Status</th>
                            <th class="text-right p-3 font-semibold">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($menus as $menu)
                        <tr class="border-b hover:bg-muted/50">
                            <td class="p-3">
                                <div class="flex items-center gap-2">
                                    @if($menu->icon)
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                                        </svg>
                                    @endif
                                    <span class="font-medium">{{ $menu->label }}</span>
                                </div>
                            </td>
                            <td class="p-3 text-sm text-muted-foreground">
                                {{ $menu->route ? 'route: ' . $menu->route : ($menu->url ?: '-') }}
                            </td>
                            <td class="p-3">
                                <span class="px-2 py-1 rounded text-xs bg-muted">{{ $menu->target }}</span>
                            </td>
                            <td class="p-3">{{ $menu->sort_order }}</td>
                            <td class="p-3">
                                <span class="px-2 py-1 rounded text-xs {{ $menu->is_active ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : 'bg-gray-100 text-gray-800 dark:bg-gray-800 dark:text-gray-200' }}">
                                    {{ $menu->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td class="p-3">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('admin.menus.edit', $menu->id) }}">
                                        <button class="inline-flex items-center justify-center gap-2 whitespace-nowrap rounded-md text-sm font-medium min-h-9 w-9 border border-input bg-background text-foreground hover-elevate active-elevate-2">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                            </svg>
                                        </button>
                                    </a>
                                    <form method="POST" action="{{ route('admin.menus.destroy', $menu->id) }}" onsubmit="return confirm('Are you sure? This action cannot be undone.');" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="inline-flex items-center justify-center gap-2 whitespace-nowrap rounded-md text-sm font-medium min-h-9 w-9 border border-input bg-background text-foreground hover-elevate active-elevate-2">
                                            <svg class="w-4 h-4 text-destructive" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            </svg>
                                        </button>
                                    </form>
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
                    No menu items yet
                </p>
                <a href="{{ route('admin.menus.create') }}">
                    <button class="inline-flex items-center justify-center gap-2 whitespace-nowrap rounded-md text-sm font-medium min-h-10 px-4 py-2 bg-primary text-primary-foreground border border-primary-border hover-elevate active-elevate-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                        Create First Menu Item
                    </button>
                </a>
            </div>
        @endif
    </div>
</div>
@endsection
