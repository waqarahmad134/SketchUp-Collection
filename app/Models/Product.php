<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'slug',
        'description',
        'full_description',
        'image',
        'image_alt',
        'images',
        'price',
        'original_price',
        'is_bundle',
        'is_digital',
        'category_id',
        'features',
        'download_links',
        'download_file',
        'sample_file',
        'file_size',
        'sketchup_version',
        'file_count',
        'download_count',
        'included_products',
        'is_active',
        'sort_order',
        // SEO fields
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
    ];

    protected $appends = [
        'image_url',
        'discount_percentage',
    ];

    protected $casts = [
        'images' => 'array',
        'features' => 'array',
        'download_links' => 'array',
        'included_products' => 'array',
        'schema_markup' => 'array',
        'is_bundle' => 'boolean',
        'is_digital' => 'boolean',
        'is_active' => 'boolean',
        'download_count' => 'integer',
        'price' => 'decimal:2',
        'original_price' => 'decimal:2',
    ];

    public function category()
    {
        return $this->belongsTo(ProductCategory::class, 'category_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class);
    }

    public function getImageUrlAttribute(): ?string
    {
        if (empty($this->image)) {
            return null;
        }

        if (Str::startsWith($this->image, ['http://', 'https://'])) {
            return $this->image;
        }

        // Use public disk for images
        return Storage::disk('public')->url($this->image);
    }

    public function getDownloadFileUrlAttribute(): ?string
    {
        if (!$this->download_file) {
            return null;
        }

        return route('bundles.download', $this->slug);
    }

    /**
     * SEO alt text for the main image, falling back to the product title.
     */
    public function getImageAltTextAttribute(): string
    {
        return $this->image_alt ?: $this->title;
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($product) {
            if (empty($product->slug)) {
                $product->slug = Str::slug($product->title);
            }
        });
    }

    public function getDiscountPercentageAttribute()
    {
        if ($this->original_price > 0) {
            return round((1 - $this->price / $this->original_price) * 100);
        }
        return 0;
    }

    /**
     * Get the included products as a collection of Product models
     * Only returns active products that exist
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getIncludedProductsModelsAttribute()
    {
        if (empty($this->included_products)) {
            return collect();
        }

        $ids = is_array($this->included_products) 
            ? $this->included_products 
            : json_decode($this->included_products, true) ?? [];

        if (empty($ids)) {
            return collect();
        }

        return Product::whereIn('id', $ids)
            ->where('is_active', true)
            ->get();
    }

    /**
     * Check if this product includes a specific product
     *
     * @param int $productId
     * @return bool
     */
    public function includesProduct(int $productId): bool
    {
        if (empty($this->included_products)) {
            return false;
        }

        $ids = is_array($this->included_products) 
            ? $this->included_products 
            : json_decode($this->included_products, true) ?? [];

        return in_array($productId, $ids);
    }

    /**
     * Get the total value of included products (sum of their prices)
     *
     * @return float
     */
    public function getIncludedProductsTotalValueAttribute(): float
    {
        return $this->included_products_models->sum('price');
    }

    /**
     * Get the savings percentage when buying as bundle vs individually
     *
     * @return float
     */
    public function getBundleSavingsPercentageAttribute(): float
    {
        if (!$this->is_bundle || empty($this->included_products_models)) {
            return 0;
        }

        $totalValue = $this->included_products_total_value;
        if ($totalValue <= 0) {
            return 0;
        }

        return round((1 - $this->price / $totalValue) * 100, 2);
    }
}
