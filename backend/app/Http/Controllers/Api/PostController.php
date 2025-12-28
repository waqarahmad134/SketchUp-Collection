<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Post;
use Illuminate\Http\Request;

class PostController extends Controller
{
    public function index(Request $request)
    {
        $query = Post::where('status', 'published')
                    ->whereNotNull('published_at')
                    ->where('published_at', '<=', now())
                    ->with('user:id,name');

        // Filter by category
        if ($request->has('category')) {
            $query->where('category', $request->category);
        }

        // Filter by featured
        if ($request->has('is_featured')) {
            $query->where('is_featured', $request->boolean('is_featured'));
        }

        // Search
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('excerpt', 'like', "%{$search}%")
                  ->orWhere('content', 'like', "%{$search}%");
            });
        }

        $posts = $query->orderBy('published_at', 'desc')
                      ->paginate($request->get('per_page', 15));

        return response()->json($posts);
    }

    public function show($slug)
    {
        $post = Post::where('status', 'published')
                   ->where(function ($query) use ($slug) {
                       $query->where('id', $slug)
                             ->orWhere('slug', $slug);
                   })
                   ->with('user:id,name')
                   ->firstOrFail();

        // Increment views
        $post->increment('views');

        return response()->json($post);
    }
}

