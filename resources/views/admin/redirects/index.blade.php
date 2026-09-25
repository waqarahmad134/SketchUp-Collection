@extends('layouts.admin')

@section('title', 'Redirects')

@section('content')
<div class="p-6 md:p-8">
    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-3xl font-bold text-foreground mb-2">Redirects</h1>
            <p class="text-muted-foreground">Manage 301/302 redirects to preserve SEO when URLs change</p>
        </div>
        <a href="{{ route('admin.redirects.create') }}"
            class="inline-flex items-center gap-2 rounded-md text-sm font-medium min-h-10 px-4 py-2 bg-primary text-primary-foreground border border-primary-border hover-elevate">
            New Redirect
        </a>
    </div>

    <div class="shadcn-card rounded-xl border bg-card border-card-border text-card-foreground shadow-sm p-6">
        @if($redirects->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b">
                            <th class="text-left p-3 font-semibold">From</th>
                            <th class="text-left p-3 font-semibold">To</th>
                            <th class="text-left p-3 font-semibold">Type</th>
                            <th class="text-left p-3 font-semibold">Hits</th>
                            <th class="text-left p-3 font-semibold">Status</th>
                            <th class="text-right p-3 font-semibold">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($redirects as $redirect)
                            <tr class="border-b hover:bg-muted/50">
                                <td class="p-3 font-mono text-sm">{{ $redirect->from_path }}</td>
                                <td class="p-3 font-mono text-sm">{{ $redirect->to_path }}</td>
                                <td class="p-3">{{ $redirect->status_code }}</td>
                                <td class="p-3">{{ number_format($redirect->hits) }}</td>
                                <td class="p-3">
                                    <span class="px-2 py-1 rounded text-xs {{ $redirect->is_active ? 'bg-green-500/20 text-green-400' : 'bg-muted text-muted-foreground' }}">
                                        {{ $redirect->is_active ? 'Active' : 'Disabled' }}
                                    </span>
                                </td>
                                <td class="p-3 text-right whitespace-nowrap">
                                    <a href="{{ route('admin.redirects.edit', $redirect) }}" class="text-sm text-cyan-400 hover:underline mr-3">Edit</a>
                                    <form method="POST" action="{{ route('admin.redirects.destroy', $redirect) }}" class="inline" onsubmit="return confirm('Delete this redirect?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-sm text-red-400 hover:underline">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="mt-6">{{ $redirects->links() }}</div>
        @else
            <p class="text-muted-foreground text-center py-12">No redirects yet. Create one when you rename or remove a URL.</p>
        @endif
    </div>
</div>
@endsection
