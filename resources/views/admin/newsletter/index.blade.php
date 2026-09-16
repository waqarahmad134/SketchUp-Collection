@extends('layouts.admin')

@section('title', 'Newsletter Subscribers')

@section('content')
<div class="p-6 md:p-8">
    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-3xl font-bold text-foreground mb-2">Newsletter</h1>
            <p class="text-muted-foreground">{{ $activeCount }} active subscriber(s)</p>
        </div>
        <a href="{{ route('admin.newsletter.export') }}"
            class="inline-flex items-center gap-2 rounded-md text-sm font-medium min-h-10 px-4 py-2 border border-input bg-background hover-elevate">
            Export CSV
        </a>
    </div>

    <div class="shadcn-card rounded-xl border bg-card border-card-border text-card-foreground shadow-sm p-6">
        <form method="GET" class="flex gap-3 mb-6">
            <input type="text" name="q" value="{{ request('q') }}" placeholder="Search email..."
                class="flex-1 rounded-md border border-input bg-background px-4 py-2 text-sm">
            <select name="status" class="rounded-md border border-input bg-background px-4 py-2 text-sm">
                <option value="">All</option>
                <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Unsubscribed</option>
            </select>
            <button type="submit" class="rounded-md text-sm font-medium min-h-10 px-4 py-2 bg-primary text-primary-foreground border border-primary-border hover-elevate">
                Search
            </button>
        </form>

        @if($subscribers->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b">
                            <th class="text-left p-3 font-semibold">Email</th>
                            <th class="text-left p-3 font-semibold">Name</th>
                            <th class="text-left p-3 font-semibold">Status</th>
                            <th class="text-left p-3 font-semibold">Subscribed</th>
                            <th class="text-right p-3 font-semibold">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($subscribers as $subscriber)
                            <tr class="border-b hover:bg-muted/50">
                                <td class="p-3 font-medium">{{ $subscriber->email }}</td>
                                <td class="p-3">{{ $subscriber->name ?? '-' }}</td>
                                <td class="p-3">
                                    <span class="px-2 py-1 rounded text-xs {{ $subscriber->is_active ? 'bg-green-500/20 text-green-400' : 'bg-muted text-muted-foreground' }}">
                                        {{ $subscriber->is_active ? 'Active' : 'Unsubscribed' }}
                                    </span>
                                </td>
                                <td class="p-3 text-sm text-muted-foreground">{{ $subscriber->created_at->format('M d, Y') }}</td>
                                <td class="p-3 text-right">
                                    <form method="POST" action="{{ route('admin.newsletter.destroy', $subscriber) }}" onsubmit="return confirm('Remove this subscriber?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-sm text-red-400 hover:underline">Remove</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="mt-6">{{ $subscribers->links() }}</div>
        @else
            <p class="text-muted-foreground text-center py-12">No subscribers yet.</p>
        @endif
    </div>
</div>
@endsection
