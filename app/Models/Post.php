<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Post extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'slug',
        'excerpt',
        'content',
        'featured_image',
        'images',
        'category_id',
        'status',
        'published_at',
        'views',
        'is_featured',
        'meta_title',
        'meta_description',
        'canonical_url',
        'robots_index',
        'robots_follow',
        'og_title',
        'og_description',
        'og_image',
        'og_type',
        'twitter_card',
        'twitter_title',
        'twitter_description',
        'twitter_image',
        'schema_markup',
        'featured_image_alt',
        'focus_keyword',
    ];

    protected $appends = [
        'featured_image_url',
    ];

    protected $casts = [
        'images' => 'array',
        'published_at' => 'datetime',
        'is_featured' => 'boolean',
        'schema_markup' => 'array',
    ];

    public function category()
    {
        return $this->belongsTo(PostCategory::class, 'category_id');
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function approvedComments()
    {
        return $this->hasMany(Comment::class)->where('status', 'approved');
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($post) {
            if (empty($post->slug)) {
                $post->slug = Str::slug($post->title);
            }
            if (empty($post->user_id)) {
                $post->user_id = auth()->id();
            }
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getFeaturedImageUrlAttribute(): ?string
    {
        if (empty($this->featured_image)) {
            return null;
        }

        if (Str::startsWith($this->featured_image, ['http://', 'https://'])) {
            return $this->featured_image;
        }

        return Storage::url($this->featured_image);
    }

    /**
     * SEO alt text for the featured image, falling back to the post title.
     */
    public function getFeaturedImageAltTextAttribute(): string
    {
        return $this->featured_image_alt ?: $this->title;
    }

    /**
     * Estimated reading time in minutes (200 words per minute).
     */
    public function getReadingTimeAttribute(): int
    {
        $words = str_word_count(strip_tags($this->content ?? ''));

        return max(1, (int) ceil($words / 200));
    }

    /**
     * Table of contents extracted from h2/h3 headings in the content.
     * Returns array of ['level' => 2|3, 'id' => slug, 'text' => heading].
     */
    public function getTableOfContentsAttribute(): array
    {
        $toc = [];

        if (preg_match_all('/<h([23])[^>]*>(.*?)<\/h[23]>/is', $this->content ?? '', $matches, PREG_SET_ORDER)) {
            foreach ($matches as $match) {
                $text = trim(strip_tags($match[2]));
                if ($text === '') {
                    continue;
                }
                $toc[] = [
                    'level' => (int) $match[1],
                    'id' => Str::slug($text),
                    'text' => $text,
                ];
            }
        }

        return $toc;
    }

    /**
     * Content with id anchors injected into h2/h3 headings so the
     * table of contents can link to them.
     */
    public function getContentWithAnchorsAttribute(): string
    {
        return preg_replace_callback(
            '/<h([23])([^>]*)>(.*?)<\/h[23]>/is',
            function ($match) {
                $text = trim(strip_tags($match[3]));
                $id = Str::slug($text);

                // Keep existing id if present.
                if (str_contains($match[2], 'id=')) {
                    return $match[0];
                }

                return "<h{$match[1]}{$match[2]} id=\"{$id}\">{$match[3]}</h{$match[1]}>";
            },
            $this->content ?? ''
        );
    }

    public function scopePublished($query)
    {
        return $query->where('status', 'published')
                    ->where(function ($q) {
                        $q->whereNull('published_at')
                            ->orWhere('published_at', '<=', now());
                    });
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }
}
