<?xml version="1.0" encoding="UTF-8"?>
<rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom">
    <channel>
        <title>{{ config('app.name') }} Blog</title>
        <link>{{ url('/blog') }}</link>
        <description>Tips, tutorials, and insights about 3D design and SketchUp.</description>
        <language>en-us</language>
        <atom:link href="{{ route('blog.feed') }}" rel="self" type="application/rss+xml" />
        <lastBuildDate>{{ now()->toRfc2822String() }}</lastBuildDate>
        @foreach($posts as $post)
        <item>
            <title>{{ $post->title }}</title>
            <link>{{ route('blog.show', $post->slug) }}</link>
            <guid isPermaLink="true">{{ route('blog.show', $post->slug) }}</guid>
            <description><![CDATA[{!! $post->excerpt ?? \Str::limit(strip_tags($post->content), 300) !!}]]></description>
            @if($post->category)
            <category>{{ $post->category->name }}</category>
            @endif
            @if($post->published_at)
            <pubDate>{{ $post->published_at->toRfc2822String() }}</pubDate>
            @endif
            @if($post->user)
            <author>{{ $post->user->email ?? $post->user->name }}</author>
            @endif
        </item>
        @endforeach
    </channel>
</rss>
