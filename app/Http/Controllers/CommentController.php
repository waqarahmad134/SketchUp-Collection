<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Post;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    /**
     * Store a blog comment. Comments start as pending and need admin approval.
     */
    public function store(Request $request, string $slug): RedirectResponse
    {
        $post = Post::where('slug', $slug)
            ->where('status', 'published')
            ->firstOrFail();

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'body' => ['required', 'string', 'max:2000'],
        ]);

        $user = $request->user();

        Comment::create([
            'post_id' => $post->id,
            'user_id' => $user?->id,
            'name' => $user?->name ?? $data['name'],
            'email' => $user?->email ?? ($data['email'] ?? null),
            'body' => $data['body'],
            'status' => 'pending',
        ]);

        return back()->with('status', 'Thank you! Your comment will appear after approval.');
    }
}
