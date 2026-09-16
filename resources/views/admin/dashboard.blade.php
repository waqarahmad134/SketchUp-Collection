@extends('layouts.admin')

@section('title', 'Dashboard')

@php
$publishedPosts = $posts->where('status', 'published');
$draftPosts = $posts->where('status', 'draft');
$totalViews = $posts->sum('view_count');
@endphp

@section('content')
<div class="p-6 md:p-8">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-foreground mb-2" data-testid="text-dashboard-title">
            Dashboard
        </h1>
        <p class="text-muted-foreground">
            Overview of your blog and content
        </p>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Total Posts -->
        <div class="shadcn-card rounded-xl border bg-card border-card-border text-card-foreground shadow-sm">
            <div class="flex flex-col space-y-1.5 p-6">
                <div class="flex items-center justify-between">
                    <h3 class="text-sm font-medium">Total Posts</h3>
                    <svg class="w-6 h-6 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                </div>
            </div>
            <div class="p-6 pt-0">
                <div class="text-2xl font-bold" data-testid="text-stat-value-0">
                    {{ $posts->count() }}
                </div>
                <p class="text-xs text-muted-foreground mt-1">
                    {{ $publishedPosts->count() }} published, {{ $draftPosts->count() }} drafts
                </p>
            </div>
        </div>

        <!-- Total Views -->
        <div class="shadcn-card rounded-xl border bg-card border-card-border text-card-foreground shadow-sm">
            <div class="flex flex-col space-y-1.5 p-6">
                <div class="flex items-center justify-between">
                    <h3 class="text-sm font-medium">Total Views</h3>
                    <svg class="w-6 h-6 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                    </svg>
                </div>
            </div>
            <div class="p-6 pt-0">
                <div class="text-2xl font-bold" data-testid="text-stat-value-1">
                    {{ $totalViews }}
                </div>
                <p class="text-xs text-muted-foreground mt-1">
                    Total views
                </p>
            </div>
        </div>

        <!-- Categories -->
        <div class="shadcn-card rounded-xl border bg-card border-card-border text-card-foreground shadow-sm">
            <div class="flex flex-col space-y-1.5 p-6">
                <div class="flex items-center justify-between">
                    <h3 class="text-sm font-medium">Categories</h3>
                    <svg class="w-6 h-6 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 19a2 2 0 01-2-2V7a2 2 0 012-2h4l2 2h12a2 2 0 012 2v1M5 19h14a2 2 0 002-2v-5a2 2 0 00-2-2H9a2 2 0 00-2 2v5a2 2 0 01-2 2z"></path>
                    </svg>
                </div>
            </div>
            <div class="p-6 pt-0">
                <div class="text-2xl font-bold" data-testid="text-stat-value-2">
                    {{ $categories->count() }}
                </div>
                <p class="text-xs text-muted-foreground mt-1">
                    Active categories
                </p>
            </div>
        </div>

        <!-- Tags -->
        <div class="shadcn-card rounded-xl border bg-card border-card-border text-card-foreground shadow-sm">
            <div class="flex flex-col space-y-1.5 p-6">
                <div class="flex items-center justify-between">
                    <h3 class="text-sm font-medium">Tags</h3>
                    <svg class="w-6 h-6 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                    </svg>
                </div>
            </div>
            <div class="p-6 pt-0">
                <div class="text-2xl font-bold" data-testid="text-stat-value-3">
                    {{ $tags->count() }}
                </div>
                <p class="text-xs text-muted-foreground mt-1">
                    Available tags
                </p>
            </div>
        </div>
    </div>

    <!-- Recent Posts -->
    <div class="shadcn-card rounded-xl border bg-card border-card-border text-card-foreground shadow-sm">
        <div class="flex flex-col space-y-1.5 p-6">
            <h2 class="text-lg font-semibold leading-none tracking-tight">Recent Posts</h2>
        </div>
        <div class="p-6 pt-0">
            @if($posts->count() > 0)
                <div class="space-y-4">
                    @foreach($posts->take(10) as $post)
                    <div
                        class="flex items-center justify-between p-4 rounded-lg border hover-elevate"
                        data-testid="post-row-{{ $post->id }}"
                    >
                        <div class="flex-1">
                            <h3 class="font-medium">{{ $post->title }}</h3>
                            <p class="text-sm text-muted-foreground">
                                {{ $post->category->name ?? 'No category' }} • {{ $post->view_count }} views
                            </p>
                        </div>
                        <div class="flex items-center gap-2">
                            <span
                                class="px-2 py-1 rounded text-xs {{ $post->status === 'published' ? 'bg-chart-3/20 text-chart-3' : 'bg-chart-4/20 text-chart-4' }}"
                            >
                                {{ $post->status === 'published' ? 'Published' : 'Draft' }}
                            </span>
                        </div>
                    </div>
                    @endforeach
                </div>
            @else
                <p class="text-center text-muted-foreground py-8">
                    No posts yet. Create your first post!
                </p>
            @endif
        </div>
    </div>
</div>
@endsection
