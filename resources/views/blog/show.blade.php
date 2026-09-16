@extends('layouts.app')

@section('content')
    <article class="py-16 bg-background">
        <div class="container mx-auto px-4 max-w-4xl">
            <a href="{{ url('/blog') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl border border-border text-sm font-medium hover:border-foreground transition mb-8">
                <i data-lucide="arrow-left" class="w-4 h-4"></i>
                Back to Blog
            </a>

            <header class="mb-8">
                <h1 class="text-4xl md:text-5xl font-bold font-display mb-6 gradient-text">
                    {{ $post->title }}
                </h1>
                <div class="flex items-center gap-6 text-muted-foreground mb-8 flex-wrap">
                    @if($post->user)
                        <span class="flex items-center gap-2">
                            <i data-lucide="user" class="w-5 h-5"></i>
                            {{ $post->user->name }}
                        </span>
                    @endif
                    @if($post->published_at)
                        <span class="flex items-center gap-2">
                            <i data-lucide="calendar" class="w-5 h-5"></i>
                            {{ $post->published_at->format('F j, Y') }}
                        </span>
                    @endif
                </div>
            </header>

            @if($post->featured_image_url)
                <div class="relative w-full h-96 rounded-3xl overflow-hidden mb-12 shadow-2xl">
                    <img src="{{ $post->featured_image_url }}" alt="{{ $post->title }}" class="w-full h-full object-cover rounded-3xl">
                </div>
            @endif

            <div class="prose prose-invert prose-lg max-w-none blog-content">
                @if($post->content)
                    {!! $post->content !!}
                @elseif($post->excerpt)
                    <p class="text-xl text-muted-foreground leading-relaxed">{{ $post->excerpt }}</p>
                @else
                    <p class="text-xl text-muted-foreground leading-relaxed">{{ $post->title }}</p>
                @endif
            </div>

            <div class="mt-12 pt-8 border-t border-border">
                <a href="{{ url('/blog') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-gradient-to-r from-cyan-500 to-violet-500 text-background font-semibold shadow-lg hover:shadow-xl transition">
                    <i data-lucide="arrow-left" class="w-4 h-4"></i>
                    Back to Blog
                </a>
            </div>

            @if($relatedPosts->count() > 0)
                <div class="mt-16">
                    <h2 class="text-2xl font-bold font-display mb-2">Related <span class="gradient-text">Articles</span></h2>
                    <p class="text-muted-foreground mb-8">Keep learning with these posts</p>
                    <div class="grid md:grid-cols-3 gap-6">
                        @foreach($relatedPosts as $related)
                            <a href="{{ url('/blog/' . $related->slug) }}" class="glass-card rounded-3xl overflow-hidden group hover:border-cyan-500/50 transition-all duration-300">
                                @if($related->featured_image_url)
                                    <div class="h-40 overflow-hidden">
                                        <img src="{{ $related->featured_image_url }}" alt="{{ $related->title }}" loading="lazy" class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110">
                                    </div>
                                @endif
                                <div class="p-5">
                                    <h3 class="font-semibold mb-2 line-clamp-2 group-hover:text-cyan-400 transition-colors">{{ $related->title }}</h3>
                                    @if($related->excerpt)
                                        <p class="text-sm text-muted-foreground line-clamp-2 mb-3">{{ $related->excerpt }}</p>
                                    @endif
                                    <span class="inline-flex items-center gap-1 text-sm text-cyan-400 font-medium">
                                        Read article <i data-lucide="arrow-right" class="w-4 h-4"></i>
                                    </span>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </article>

    {{-- Comments --}}
    <section class="py-12">
        <div class="container mx-auto px-4">
            <div class="max-w-3xl mx-auto">
                <h2 class="text-2xl font-bold font-display mb-6">
                    Comments <span class="text-muted-foreground text-lg font-normal">({{ $comments->count() }})</span>
                </h2>

                <div class="space-y-4 mb-10">
                    @forelse($comments as $comment)
                        <div class="glass-card rounded-2xl p-5">
                            <div class="flex items-center gap-3 mb-2">
                                <div class="w-10 h-10 rounded-full bg-gradient-to-br from-cyan-500 to-violet-500 flex items-center justify-center">
                                    <span class="text-sm font-bold text-background">{{ strtoupper(substr($comment->name, 0, 1)) }}</span>
                                </div>
                                <div>
                                    <p class="font-semibold text-sm">{{ $comment->name }}</p>
                                    <p class="text-xs text-muted-foreground">{{ $comment->created_at->format('M d, Y') }}</p>
                                </div>
                            </div>
                            <p class="text-muted-foreground text-sm">{{ $comment->body }}</p>
                        </div>
                    @empty
                        <p class="text-muted-foreground">No comments yet. Be the first to share your thoughts.</p>
                    @endforelse
                </div>

                <div class="glass-card rounded-2xl p-6">
                    <h3 class="font-bold font-display text-lg mb-4">Leave a comment</h3>
                    <form method="POST" action="{{ route('comments.store', $post->slug) }}" class="space-y-4">
                        @csrf
                        @guest
                            <div class="grid sm:grid-cols-2 gap-4">
                                <input type="text" name="name" required placeholder="Your name" value="{{ old('name') }}"
                                    class="rounded-xl border border-border bg-background px-4 py-3 text-sm focus:outline-none focus:border-cyan-500">
                                <input type="email" name="email" placeholder="Email (optional)" value="{{ old('email') }}"
                                    class="rounded-xl border border-border bg-background px-4 py-3 text-sm focus:outline-none focus:border-cyan-500">
                            </div>
                        @endguest
                        <textarea name="body" required rows="4" placeholder="Write your comment..." class="w-full rounded-xl border border-border bg-background px-4 py-3 text-sm focus:outline-none focus:border-cyan-500">{{ old('body') }}</textarea>
                        <button type="submit" class="px-8 py-3 rounded-xl bg-gradient-to-r from-cyan-500 to-violet-500 text-background font-semibold shadow-lg hover:shadow-xl transition">
                            Post Comment
                        </button>
                        <p class="text-xs text-muted-foreground">Comments are reviewed before appearing.</p>
                    </form>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('scripts')
<style>
    .blog-content {
        color: hsl(var(--foreground));
        line-height: 1.8;
    }
    .blog-content h1,
    .blog-content h2,
    .blog-content h3,
    .blog-content h4 {
        color: hsl(var(--foreground));
        font-weight: 700;
        margin-top: 2rem;
        margin-bottom: 1rem;
    }
    .blog-content h1 { font-size: 2.5rem; }
    .blog-content h2 { font-size: 2rem; }
    .blog-content h3 { font-size: 1.5rem; }
    .blog-content p {
        margin-bottom: 1.5rem;
        color: hsl(var(--muted-foreground));
    }
    .blog-content img {
        border-radius: 1rem;
        margin: 2rem 0;
    }
    .blog-content a {
        color: hsl(187 100% 50%);
        text-decoration: underline;
    }
    .blog-content ul,
    .blog-content ol {
        margin: 1.5rem 0;
        padding-left: 2rem;
    }
    .blog-content li {
        margin-bottom: 0.5rem;
    }
    .blog-content code {
        background: hsl(var(--muted));
        padding: 0.2rem 0.4rem;
        border-radius: 0.25rem;
        font-size: 0.9em;
    }
    .blog-content pre {
        background: hsl(var(--muted));
        padding: 1rem;
        border-radius: 0.5rem;
        overflow-x: auto;
        margin: 1.5rem 0;
    }
    .blog-content blockquote {
        border-left: 4px solid hsl(187 100% 50%);
        padding-left: 1rem;
        margin: 1.5rem 0;
        font-style: italic;
        color: hsl(var(--muted-foreground));
    }
</style>
@endpush

