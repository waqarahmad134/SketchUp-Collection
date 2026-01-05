@extends('layouts.admin')

@section('title', 'Posts')

@section('content')
<div class="p-6 md:p-8">
    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-3xl font-bold text-foreground mb-2" data-testid="text-posts-title">
                Posts
            </h1>
            <p class="text-muted-foreground">
                Manage all your blog posts
            </p>
        </div>
        <a href="{{ route('newadmin.posts.create') }}">
            <button class="inline-flex items-center justify-center gap-2 whitespace-nowrap rounded-md text-sm font-medium min-h-10 px-4 py-2 bg-primary text-primary-foreground border border-primary-border hover-elevate active-elevate-2" data-testid="button-create-post">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                New Post
            </button>
        </a>
    </div>

    <div class="shadcn-card rounded-xl border bg-card border-card-border text-card-foreground shadow-sm p-6">
        @if($posts->count() > 0)
            <div class="space-y-4">
                @foreach($posts as $post)
                <div
                    class="flex items-center justify-between p-4 rounded-lg border hover-elevate"
                    data-testid="post-item-{{ $post->id }}"
                >
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-3 mb-2">
                            <h3 class="font-semibold truncate">{{ $post->title }}</h3>
                            <span
                                class="px-2 py-1 rounded text-xs whitespace-nowrap {{ $post->status === 'published' ? 'bg-chart-3/20 text-chart-3' : 'bg-chart-4/20 text-chart-4' }}"
                            >
                                {{ $post->status === 'published' ? 'Published' : 'Draft' }}
                            </span>
                        </div>
                        <div class="flex items-center gap-4 text-sm text-muted-foreground">
                            <span>{{ $post->category->name ?? 'No category' }}</span>
                            <span class="flex items-center gap-1">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                </svg>
                                {{ $post->view_count }} views
                            </span>
                            <span>
                                {{ $post->created_at->format('d/m/Y') }}
                            </span>
                        </div>
                    </div>
                    
                    <div class="flex items-center gap-2 ml-4">
                        <a href="{{ route('newadmin.posts.edit', $post->id) }}">
                            <button class="inline-flex items-center justify-center gap-2 whitespace-nowrap rounded-md text-sm font-medium min-h-9 w-9 border border-input bg-background text-foreground hover-elevate active-elevate-2" data-testid="button-edit-{{ $post->id }}">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                </svg>
                            </button>
                        </a>
                        
                        <form method="POST" action="{{ route('newadmin.posts.destroy', $post->id) }}" onsubmit="return confirm('Are you sure? This action cannot be undone.');" class="inline">
                            @csrf
                            @method('DELETE')
                            <button
                                type="submit"
                                class="inline-flex items-center justify-center gap-2 whitespace-nowrap rounded-md text-sm font-medium min-h-9 w-9 border border-input bg-background text-foreground hover-elevate active-elevate-2"
                                data-testid="button-delete-{{ $post->id }}"
                            >
                                <svg class="w-4 h-4 text-destructive" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                </svg>
                            </button>
                        </form>
                    </div>
                </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-12">
                <p class="text-muted-foreground mb-4">
                    No posts yet
                </p>
                <a href="{{ route('newadmin.posts.create') }}">
                    <button class="inline-flex items-center justify-center gap-2 whitespace-nowrap rounded-md text-sm font-medium min-h-10 px-4 py-2 bg-primary text-primary-foreground border border-primary-border hover-elevate active-elevate-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                        Create First Post
                    </button>
                </a>
            </div>
        @endif
    </div>
</div>
@endsection
