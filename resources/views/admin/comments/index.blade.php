@extends('layouts.admin')

@section('title', 'Blog Comments')

@section('content')
<div class="p-6 md:p-8">
    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-3xl font-bold text-foreground mb-2">Blog Comments</h1>
            <p class="text-muted-foreground">{{ $pendingCount }} comment(s) awaiting approval</p>
        </div>
    </div>

    <div class="shadcn-card rounded-xl border bg-card border-card-border text-card-foreground shadow-sm p-6">
        <form method="GET" class="flex gap-3 mb-6">
            <select name="status" class="rounded-md border border-input bg-background px-4 py-2 text-sm" onchange="this.form.submit()">
                <option value="">All statuses</option>
                <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Approved</option>
                <option value="spam" {{ request('status') === 'spam' ? 'selected' : '' }}>Spam</option>
            </select>
        </form>

        @if($comments->count() > 0)
            <div class="space-y-4">
                @foreach($comments as $comment)
                    <div class="border border-border rounded-xl p-5">
                        <div class="flex flex-wrap items-center justify-between gap-3 mb-3">
                            <div>
                                <p class="font-semibold">{{ $comment->name }}
                                    <span class="text-sm text-muted-foreground font-normal">on</span>
                                    <a href="{{ route('blog.show', $comment->post->slug) }}" target="_blank" class="text-sm text-cyan-400 hover:underline">{{ $comment->post->title }}</a>
                                </p>
                                <p class="text-xs text-muted-foreground">{{ $comment->created_at->format('M d, Y h:i A') }} {{ $comment->email ? '- ' . $comment->email : '' }}</p>
                            </div>
                            <span class="px-2 py-1 rounded text-xs font-bold
                                {{ $comment->status === 'approved' ? 'bg-green-500/20 text-green-400' : '' }}
                                {{ $comment->status === 'pending' ? 'bg-yellow-500/20 text-yellow-400' : '' }}
                                {{ $comment->status === 'spam' ? 'bg-red-500/20 text-red-400' : '' }}">
                                {{ ucfirst($comment->status) }}
                            </span>
                        </div>
                        <p class="text-sm text-muted-foreground mb-4">{{ $comment->body }}</p>
                        <div class="flex gap-2">
                            @if($comment->status !== 'approved')
                                <form method="POST" action="{{ route('admin.comments.approve', $comment) }}">
                                    @csrf
                                    <button type="submit" class="text-xs font-semibold px-3 py-1.5 rounded-md bg-green-500/20 text-green-400 hover:bg-green-500/30">Approve</button>
                                </form>
                            @endif
                            @if($comment->status !== 'spam')
                                <form method="POST" action="{{ route('admin.comments.spam', $comment) }}">
                                    @csrf
                                    <button type="submit" class="text-xs font-semibold px-3 py-1.5 rounded-md bg-yellow-500/20 text-yellow-400 hover:bg-yellow-500/30">Mark spam</button>
                                </form>
                            @endif
                            <form method="POST" action="{{ route('admin.comments.destroy', $comment) }}" onsubmit="return confirm('Delete this comment?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-xs font-semibold px-3 py-1.5 rounded-md bg-red-500/20 text-red-400 hover:bg-red-500/30">Delete</button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>
            <div class="mt-6">{{ $comments->links() }}</div>
        @else
            <p class="text-muted-foreground text-center py-12">No comments yet.</p>
        @endif
    </div>
</div>
@endsection
