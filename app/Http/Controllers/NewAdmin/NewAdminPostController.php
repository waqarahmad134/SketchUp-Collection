<?php

namespace App\Http\Controllers\NewAdmin;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Models\PostCategory;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class NewAdminPostController extends Controller
{
    public function index()
    {
        $posts = Post::with('category')->latest()->get();
        // Add view_count accessor for compatibility
        $posts->each(function ($post) {
            $post->view_count = $post->views;
        });
        return view('new admin.admin.posts.index', compact('posts'));
    }

    public function create()
    {
        $categories = PostCategory::active()->get();
        $tags = Tag::where('type', 'post')->active()->get();
        return view('new admin.admin.posts.create', compact('categories', 'tags'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:posts,slug',
            'excerpt' => 'nullable|string',
            'content' => 'required|string',
            'featured_image' => 'nullable|image|max:2048',
            'category_id' => 'nullable|exists:post_categories,id',
            'status' => 'required|in:draft,published,archived',
            'tagIds' => 'nullable|string',
        ]);

        $postData = [
            'title' => $validated['title'],
            'slug' => $validated['slug'],
            'excerpt' => $validated['excerpt'] ?? null,
            'content' => $validated['content'],
            'category_id' => $validated['category_id'] ?? null,
            'status' => $validated['status'],
            'user_id' => auth()->id(),
            'published_at' => $validated['status'] === 'published' ? now() : null,
        ];

        if ($request->hasFile('featured_image')) {
            $postData['featured_image'] = $request->file('featured_image')->store('posts', 'public');
        }

        $post = Post::create($postData);

        // Attach tags
        if (!empty($validated['tagIds'])) {
            $tagIds = json_decode($validated['tagIds'], true);
            if (is_array($tagIds)) {
                $post->tags()->attach($tagIds);
            }
        }

        return redirect()->route('newadmin.posts.index')->with('success', 'Post created successfully');
    }

    public function show(string $id)
    {
        $post = Post::findOrFail($id);
        return view('new admin.admin.posts.show', compact('post'));
    }

    public function edit(string $id)
    {
        $post = Post::with('tags')->findOrFail($id);
        $categories = PostCategory::active()->get();
        $tags = Tag::where('type', 'post')->active()->get();
        return view('new admin.admin.posts.edit', compact('post', 'categories', 'tags'));
    }

    public function update(Request $request, string $id)
    {
        $post = Post::findOrFail($id);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:posts,slug,' . $id,
            'excerpt' => 'nullable|string',
            'content' => 'required|string',
            'featured_image' => 'nullable|image|max:2048',
            'category_id' => 'nullable|exists:post_categories,id',
            'status' => 'required|in:draft,published,archived',
            'tagIds' => 'nullable|string',
        ]);

        $postData = [
            'title' => $validated['title'],
            'slug' => $validated['slug'],
            'excerpt' => $validated['excerpt'] ?? null,
            'content' => $validated['content'],
            'category_id' => $validated['category_id'] ?? null,
            'status' => $validated['status'],
        ];

        if ($validated['status'] === 'published' && !$post->published_at) {
            $postData['published_at'] = now();
        }

        if ($request->hasFile('featured_image')) {
            // Delete old image
            if ($post->featured_image) {
                Storage::disk('public')->delete($post->featured_image);
            }
            $postData['featured_image'] = $request->file('featured_image')->store('posts', 'public');
        }

        $post->update($postData);

        // Sync tags
        if (!empty($validated['tagIds'])) {
            $tagIds = json_decode($validated['tagIds'], true);
            if (is_array($tagIds)) {
                $post->tags()->sync($tagIds);
            }
        } else {
            $post->tags()->detach();
        }

        return redirect()->route('newadmin.posts.index')->with('success', 'Post updated successfully');
    }

    public function destroy(string $id)
    {
        $post = Post::findOrFail($id);
        
        // Delete featured image
        if ($post->featured_image) {
            Storage::disk('public')->delete($post->featured_image);
        }

        $post->delete();

        return redirect()->route('newadmin.posts.index')->with('success', 'Post deleted successfully');
    }
}
