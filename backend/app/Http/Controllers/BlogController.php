<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;

class BlogController extends Controller
{
    public function index(Request $request)
    {
        $posts = Post::with('user')
            ->where('status', 'published')
            ->where(function ($query) {
                $query->whereNull('published_at')
                    ->orWhere('published_at', '<=', now());
            })
            ->latest('published_at')
            ->paginate(12);

        return view('blog.index', [
            'title' => 'Blog | 3DAssetHub',
            'metaDescription' => 'Tips, tutorials, and insights about 3D design and SketchUp.',
            'posts' => $posts,
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
        ]);
    }
}

