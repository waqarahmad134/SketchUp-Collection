<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Models\PostCategory;
use App\Models\Tag;
use Illuminate\Http\Request;
use App\Support\ImageOptimizer;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AdminPostController extends Controller
{
    public function index()
    {
        $posts = Post::with('category')->latest()->get();
        // Add view_count accessor for compatibility
        $posts->each(function ($post) {
            $post->view_count = $post->views;
        });
        return view('admin.posts.index', compact('posts'));
    }

    public function create()
    {
        $categories = PostCategory::active()->get();
        $tags = Tag::where('type', 'post')->active()->get();
        return view('admin.posts.create', compact('categories', 'tags'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:posts,slug',
            'excerpt' => 'nullable|string',
            'content' => 'required|string',
            'featured_image' => 'nullable',
            'category_id' => 'nullable|exists:post_categories,id',
            'status' => 'required|in:draft,published,archived',
            'tagIds' => 'nullable|string',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'focus_keyword' => 'nullable|string|max:255',
            'canonical_url' => 'nullable|string|max:255',
            'robots_index' => 'nullable|in:index,noindex',
            'robots_follow' => 'nullable|in:follow,nofollow',
            'featured_image_alt' => 'nullable|string|max:255',
            'og_title' => 'nullable|string|max:255',
            'og_description' => 'nullable|string|max:500',
            'og_image' => 'nullable|string|max:500',
            'twitter_card' => 'nullable|string|max:50',
            'twitter_title' => 'nullable|string|max:255',
            'twitter_description' => 'nullable|string|max:500',
            'twitter_image' => 'nullable|string|max:500',
            'schema_markup' => 'nullable|string',
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

        $postData = array_merge($postData, $this->seoFields($validated));

        if ($request->hasFile('featured_image')) {
            $postData['featured_image'] = ImageOptimizer::optimize($request->file('featured_image'), 'posts');
        } elseif ($request->filled('featured_image')) {
            // Editor form sends an image URL string
            $postData['featured_image'] = $request->input('featured_image');
        }

        $post = Post::create($postData);

        // Attach tags
        if (!empty($validated['tagIds'])) {
            $tagIds = json_decode($validated['tagIds'], true);
            if (is_array($tagIds)) {
                $post->tags()->attach($tagIds);
            }
        }

        return redirect()->route('admin.posts.index')->with('success', 'Post created successfully');
    }

    public function show(string $id)
    {
        $post = Post::findOrFail($id);
        return view('admin.posts.show', compact('post'));
    }

    public function edit(string $id)
    {
        $post = Post::with('tags')->findOrFail($id);
        $categories = PostCategory::active()->get();
        $tags = Tag::where('type', 'post')->active()->get();
        return view('admin.posts.edit', compact('post', 'categories', 'tags'));
    }

    public function update(Request $request, string $id)
    {
        $post = Post::findOrFail($id);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:posts,slug,' . $id,
            'excerpt' => 'nullable|string',
            'content' => 'required|string',
            'featured_image' => 'nullable',
            'category_id' => 'nullable|exists:post_categories,id',
            'status' => 'required|in:draft,published,archived',
            'tagIds' => 'nullable|string',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'focus_keyword' => 'nullable|string|max:255',
            'canonical_url' => 'nullable|string|max:255',
            'robots_index' => 'nullable|in:index,noindex',
            'robots_follow' => 'nullable|in:follow,nofollow',
            'featured_image_alt' => 'nullable|string|max:255',
            'og_title' => 'nullable|string|max:255',
            'og_description' => 'nullable|string|max:500',
            'og_image' => 'nullable|string|max:500',
            'twitter_card' => 'nullable|string|max:50',
            'twitter_title' => 'nullable|string|max:255',
            'twitter_description' => 'nullable|string|max:500',
            'twitter_image' => 'nullable|string|max:500',
            'schema_markup' => 'nullable|string',
        ]);

        $postData = [
            'title' => $validated['title'],
            'slug' => $validated['slug'],
            'excerpt' => $validated['excerpt'] ?? null,
            'content' => $validated['content'],
            'category_id' => $validated['category_id'] ?? null,
            'status' => $validated['status'],
        ];

        $postData = array_merge($postData, $this->seoFields($validated));

        if ($validated['status'] === 'published' && !$post->published_at) {
            $postData['published_at'] = now();
        }

        if ($request->hasFile('featured_image')) {
            // Delete old image
            if ($post->featured_image) {
                Storage::disk('public')->delete($post->featured_image);
            }
            $postData['featured_image'] = ImageOptimizer::optimize($request->file('featured_image'), 'posts');
        } elseif ($request->filled('featured_image')) {
            // Editor form sends an image URL string
            $postData['featured_image'] = $request->input('featured_image');
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

        return redirect()->route('admin.posts.index')->with('success', 'Post updated successfully');
    }

    /**
     * Extract the SEO fields from validated data, decoding custom
     * schema markup JSON when valid.
     */
    protected function seoFields(array $validated): array
    {
        $fields = [
            'meta_title' => $validated['meta_title'] ?? null,
            'meta_description' => $validated['meta_description'] ?? null,
            'focus_keyword' => $validated['focus_keyword'] ?? null,
            'canonical_url' => $validated['canonical_url'] ?? null,
            'robots_index' => $validated['robots_index'] ?? 'index',
            'robots_follow' => $validated['robots_follow'] ?? 'follow',
            'featured_image_alt' => $validated['featured_image_alt'] ?? null,
            'og_title' => $validated['og_title'] ?? null,
            'og_description' => $validated['og_description'] ?? null,
            'og_image' => $validated['og_image'] ?? null,
            'twitter_card' => $validated['twitter_card'] ?? null,
            'twitter_title' => $validated['twitter_title'] ?? null,
            'twitter_description' => $validated['twitter_description'] ?? null,
            'twitter_image' => $validated['twitter_image'] ?? null,
        ];

        $schema = trim($validated['schema_markup'] ?? '');
        if ($schema !== '') {
            $decoded = json_decode($schema, true);
            $fields['schema_markup'] = is_array($decoded) ? $decoded : null;
        } else {
            $fields['schema_markup'] = null;
        }

        return $fields;
    }
}

    public function destroy(string $id)
    {
        $post = Post::findOrFail($id);
        
        // Delete featured image
        if ($post->featured_image) {
            Storage::disk('public')->delete($post->featured_image);
        }

        $post->delete();

        return redirect()->route('admin.posts.index')->with('success', 'Post deleted successfully');
    }
}
