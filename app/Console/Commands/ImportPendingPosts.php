<?php

namespace App\Console\Commands;

use App\Models\Post;
use App\Models\PostCategory;
use App\Models\Tag;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Imports AI-prepared blog posts dropped as JSON files into database/pending-posts/.
 *
 * Each JSON file may reference a featured image placed in
 * database/pending-posts/images/<filename>. The image is moved into the
 * public disk and the post is published immediately.
 *
 * Expected JSON shape:
 * {
 *   "title": "How to ...",
 *   "slug": "how-to-...",
 *   "excerpt": "...",
 *   "content": "<p>...</p>",
 *   "meta_title": "...",
 *   "meta_description": "...",
 *   "featured_image": "2026-09-17-how-to.jpg",
 *   "category": "Tutorials",
 *   "tags": ["sketchup", "beginners"]
 * }
 *
 * Run: php artisan blog:import-pending
 * Scheduled daily in routes/console.php (server needs `php artisan schedule:run` cron).
 */
class ImportPendingPosts extends Command
{
    protected $signature = 'blog:import-pending';
    protected $description = 'Import pending blog post JSON files from database/pending-posts into published posts';

    public function handle(): int
    {
        $pendingDir = database_path('pending-posts');
        $imagesDir = $pendingDir . '/images';
        $importedDir = $pendingDir . '/imported';

        if (!is_dir($pendingDir)) {
            $this->info('No pending-posts directory found. Nothing to import.');
            return self::SUCCESS;
        }

        if (!is_dir($importedDir)) {
            mkdir($importedDir, 0755, true);
        }

        $files = glob($pendingDir . '/*.json');
        if (empty($files)) {
            $this->info('No pending posts found.');
            return self::SUCCESS;
        }

        $imported = 0;
        foreach ($files as $file) {
            $result = $this->importFile($file, $imagesDir, $importedDir);
            if ($result) {
                $imported++;
            }
        }

        $this->info("Imported {$imported} of " . count($files) . " pending posts.");
        return self::SUCCESS;
    }

    protected function importFile(string $file, string $imagesDir, string $importedDir): bool
    {
        $name = basename($file);
        $data = json_decode(file_get_contents($file), true);

        if (!is_array($data) || empty($data['title']) || empty($data['content'])) {
            $this->error("Skipping {$name}: missing title or content.");
            return false;
        }

        // Unique slug
        $slug = Str::slug($data['slug'] ?? $data['title']);
        $base = $slug;
        $i = 2;
        while (Post::where('slug', $slug)->exists()) {
            $slug = $base . '-' . $i++;
        }

        // Featured image: move from pending images dir to public disk
        $featuredImage = null;
        if (!empty($data['featured_image'])) {
            $source = $imagesDir . '/' . basename($data['featured_image']);
            if (is_file($source)) {
                $ext = strtolower(pathinfo($source, PATHINFO_EXTENSION)) ?: 'jpg';
                $target = 'blog/' . now()->format('Y/m') . '/' . $slug . '.' . $ext;
                Storage::disk('public')->put($target, file_get_contents($source));
                $featuredImage = $target;
                @unlink($source);
            } else {
                $this->warn("{$name}: featured image not found, publishing without it.");
            }
        }

        // Category: find or create
        $categoryId = null;
        if (!empty($data['category'])) {
            $category = PostCategory::firstOrCreate(
                ['slug' => Str::slug($data['category'])],
                ['name' => $data['category'], 'is_active' => true]
            );
            $categoryId = $category->id;
        }

        $post = Post::create([
            'user_id' => 1,
            'title' => $data['title'],
            'slug' => $slug,
            'excerpt' => $data['excerpt'] ?? Str::limit(strip_tags($data['content']), 160),
            'content' => $data['content'],
            'featured_image' => $featuredImage,
            'category_id' => $categoryId,
            'status' => 'published',
            'published_at' => now(),
            'meta_title' => $data['meta_title'] ?? null,
            'meta_description' => $data['meta_description'] ?? null,
        ]);

        // Tags: find or create, then attach
        if (!empty($data['tags']) && is_array($data['tags'])) {
            $tagIds = [];
            foreach ($data['tags'] as $tagName) {
                $tagName = trim($tagName);
                if ($tagName === '') {
                    continue;
                }
                $tag = Tag::firstOrCreate(
                    ['slug' => Str::slug($tagName)],
                    ['name' => $tagName, 'is_active' => true]
                );
                $tagIds[] = $tag->id;
            }
            if ($tagIds) {
                $post->tags()->syncWithoutDetaching($tagIds);
            }
        }

        rename($file, $importedDir . '/' . $name);
        $this->info("Published: {$post->title} (/blog/{$slug})");

        return true;
    }
}
