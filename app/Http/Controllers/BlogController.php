<?php

namespace App\Http\Controllers;

use App\Models\Post;
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

        return view('blog.show', [
            'title' => $post->title . ' | Blog',
            'metaDescription' => $post->meta_description ?? $post->excerpt ?? $post->title,
            'post' => $post,
            'seoModel' => $post,
        ]);
    }
}

