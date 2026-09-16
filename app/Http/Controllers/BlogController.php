<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\PostCategory;
use App\Models\Tag;
use Illuminate\Http\Request;

class BlogController extends Controller
{
    public function index(Request $request)
    {
        $query = Post::with('user')
            ->where('status', 'published')
            ->where(function ($query) {
                $query->whereNull('published_at')
                    ->orWhere('published_at', '<=', now());
            });

        // Search by keyword
        if ($request->filled('q')) {
            $search = trim($request->input('q'));
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('excerpt', 'like', "%{$search}%")
                    ->orWhere('content', 'like', "%{$search}%");
            });
        }

        // Sort order
        $sortBy = $request->get('sort', 'newest');
        switch ($sortBy) {
            case 'oldest':
                $query->oldest('published_at');
                break;
            case 'title_asc':
                $query->orderBy('title', 'asc');
                break;
            case 'title_desc':
                $query->orderBy('title', 'desc');
                break;
            default:
                $query->latest('published_at');
                break;
        }

        // Pagination
        $perPage = $request->get('per_page', 12);
        $perPage = in_array($perPage, [9, 12, 18, 24, 36]) ? $perPage : 12;
        $posts = $query->paginate($perPage)->withQueryString();

        // Normalize filter values for view
        $filters = [
            'q' => $request->get('q', ''),
            'sort' => $request->get('sort', 'newest'),
            'per_page' => $perPage,
            'view' => $request->get('view', 'grid'),
            'columns' => $request->get('columns', '3'),
        ];

        // If AJAX request, return JSON
        if ($request->ajax()) {
            return response()->json([
                'posts' => $posts->items(),
                'pagination' => [
                    'current_page' => $posts->currentPage(),
                    'last_page' => $posts->lastPage(),
                    'per_page' => $posts->perPage(),
                    'total' => $posts->total(),
                    'from' => $posts->firstItem(),
                    'to' => $posts->lastItem(),
                    'has_more' => $posts->hasMorePages(),
                    'prev_url' => $posts->previousPageUrl(),
                    'next_url' => $posts->nextPageUrl(),
                ],
            ]);
        }

        return view('blog.index', [
            'title' => 'Blog | SketchUp Collection',
            'metaDescription' => 'Tips, tutorials, and insights about 3D design and SketchUp.',
            'posts' => $posts,
            'filters' => $filters,
        ]);
    }

    public function show(string $slug)
    {
        $post = Post::with('user')
            ->where('slug', $slug)
            ->where('status', 'published')
            ->where(function ($query) {
                $query->whereNull('published_at')
                    ->orWhere('published_at', '<=', now());
            })
            ->firstOrFail();

        // Related articles for internal linking (course rule M24):
        // same category first, then fill with other published posts.
        $relatedPosts = Post::where('status', 'published')
            ->where('id', '!=', $post->id)
            ->where(function ($query) {
                $query->whereNull('published_at')
                    ->orWhere('published_at', '<=', now());
            })
            ->when($post->category_id, fn ($q) => $q->where('category_id', $post->category_id))
            ->latest('published_at')
            ->take(3)
            ->get();

        if ($relatedPosts->count() < 3) {
            $fallback = Post::where('status', 'published')
                ->where('id', '!=', $post->id)
                ->whereNotIn('id', $relatedPosts->pluck('id'))
                ->where(function ($query) {
                    $query->whereNull('published_at')
                        ->orWhere('published_at', '<=', now());
                })
                ->latest('published_at')
                ->take(3 - $relatedPosts->count())
                ->get();
            $relatedPosts = $relatedPosts->merge($fallback);
        }

        return view('blog.show', [
            'title' => $post->title . ' | Blog',
            'metaDescription' => $post->meta_description ?? $post->excerpt ?? $post->title,
            'post' => $post,
            'relatedPosts' => $relatedPosts,
            'comments' => $post->approvedComments()->latest()->get(),
            'seoModel' => $post,
            'prevPost' => $this->adjacentPost($post, 'prev'),
            'nextPost' => $this->adjacentPost($post, 'next'),
        ]);
    }

    /**
     * Category archive: /blog/category/{slug}
     */
    public function category(string $slug)
    {
        $category = PostCategory::where('slug', $slug)->where('is_active', true)->firstOrFail();

        $posts = $this->publishedQuery()
            ->where('category_id', $category->id)
            ->latest('published_at')
            ->paginate(12);

        return view('blog.index', [
            'title' => $category->meta_title ?: $category->name . ' Articles | Blog',
            'metaDescription' => $category->meta_description ?: $category->description ?: "Articles in the {$category->name} category.",
            'posts' => $posts,
            'filters' => ['q' => '', 'sort' => 'newest', 'per_page' => 12, 'view' => 'grid', 'columns' => '3'],
            'activeCategory' => $category,
            'seoModel' => $category,
        ]);
    }

    /**
     * Tag archive: /blog/tag/{slug}
     */
    public function tag(string $slug)
    {
        $tag = Tag::where('slug', $slug)->where('type', 'post')->where('is_active', true)->firstOrFail();

        $posts = $this->publishedQuery()
            ->whereHas('tags', fn ($q) => $q->where('tags.id', $tag->id))
            ->latest('published_at')
            ->paginate(12);

        return view('blog.index', [
            'title' => "Articles tagged \"{$tag->name}\" | Blog",
            'metaDescription' => $tag->description ?: "All articles tagged with {$tag->name}.",
            'posts' => $posts,
            'filters' => ['q' => '', 'sort' => 'newest', 'per_page' => 12, 'view' => 'grid', 'columns' => '3'],
            'activeTag' => $tag,
            'seoModel' => $tag,
        ]);
    }

    /**
     * RSS feed of the latest published posts.
     */
    public function feed()
    {
        $posts = $this->publishedQuery()->latest('published_at')->take(20)->get();

        return response()
            ->view('blog.feed', ['posts' => $posts])
            ->header('Content-Type', 'application/rss+xml; charset=utf-8');
    }

    protected function publishedQuery()
    {
        return Post::with('user')
            ->where('status', 'published')
            ->where(function ($query) {
                $query->whereNull('published_at')
                    ->orWhere('published_at', '<=', now());
            });
    }

    /**
     * Previous / next published post by publish date.
     */
    protected function adjacentPost(Post $post, string $direction): ?Post
    {
        $query = $this->publishedQuery()->where('id', '!=', $post->id);

        if ($direction === 'prev') {
            return $query->where('published_at', '<=', $post->published_at ?? now())
                ->orderBy('published_at', 'desc')
                ->first();
        }

        return $query->where('published_at', '>=', $post->published_at ?? now())
            ->orderBy('published_at', 'asc')
            ->first();
    }
}

