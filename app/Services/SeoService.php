<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\Facades\URL;

class SeoService
{
    /**
     * Route name patterns that must never appear in search results
     * (cart, checkout, auth). Course rule M25: noindex for utility pages.
     */
    protected array $noindexRoutePatterns = [
        'login',
        'login.submit',
        'register',
        'register.submit',
        'cart.*',
        'checkout.*',
    ];

    /**
     * Map model class to its Schema.org type so markup reflects
     * the visible page content (course rule M27).
     */
    protected function schemaTypeFor($model): string
    {
        if ($model instanceof \App\Models\Product) {
            return 'Product';
        }

        if ($model instanceof \App\Models\Post) {
            return 'BlogPosting';
        }

        return 'WebPage';
    }

    /**
     * Generate meta title
     */
    public function getTitle($model = null): string
    {
        if ($model && $model->meta_title) {
            return $model->meta_title;
        }

        $siteName = Setting::get('site_name') ?? config('app.name');

        if ($model && isset($model->title)) {
            return $model->title . ' - ' . $siteName;
        }

        // Category-style models use `name` instead of `title`.
        if ($model && isset($model->name)) {
            return $model->name . ' - ' . $siteName;
        }

        // Use default_meta_title, fallback to site_name, then config
        return Setting::get('default_meta_title') ?? Setting::get('site_name') ?? config('app.name') ?? 'Default Title';
    }

    /**
     * Generate meta description
     */
    public function getDescription($model = null): string
    {
        if ($model && $model->meta_description) {
            return $model->meta_description;
        }

        if ($model && isset($model->description)) {
            return \Str::limit(strip_tags($model->description), 155);
        }

        if ($model && isset($model->excerpt)) {
            return \Str::limit(strip_tags($model->excerpt), 155);
        }

        return Setting::get('default_meta_description') ?? Setting::get('site_tagline') ?? '';
    }

    /**
     * Get canonical URL
     */
    public function getCanonicalUrl($model = null): string
    {
        if ($model && $model->canonical_url) {
            return $model->canonical_url;
        }

        return URL::current();
    }

    /**
     * Get robots meta tag
     */
    public function getRobotsMeta($model = null): string
    {
        // Utility routes (cart, checkout, auth) are never indexable (M25).
        $routeName = request()->route()?->getName() ?? '';
        foreach ($this->noindexRoutePatterns as $pattern) {
            if (fnmatch($pattern, $routeName)) {
                return 'noindex, nofollow';
            }
        }

        $index = $model->robots_index ?? Setting::get('robots_index', 'index');
        $follow = $model->robots_follow ?? Setting::get('robots_follow', 'follow');
        
        return "{$index}, {$follow}";
    }

    /**
     * Get Open Graph title
     */
    public function getOgTitle($model = null): string
    {
        if ($model && $model->og_title) {
            return $model->og_title;
        }

        return $this->getTitle($model);
    }

    /**
     * Get Open Graph description
     */
    public function getOgDescription($model = null): string
    {
        if ($model && $model->og_description) {
            return $model->og_description;
        }

        return $this->getDescription($model);
    }

    /**
     * Get Open Graph image
     */
    public function getOgImage($model = null): string
    {
        if ($model && $model->og_image) {
            return URL::to($model->og_image);
        }

        if ($model && isset($model->image)) {
            return URL::to($model->image);
        }

        if ($model && isset($model->featured_image)) {
            return URL::to($model->featured_image);
        }

        $default = Setting::get('og_default_image') ?? '/assets/og-default.jpg';
        return URL::to($default);
    }

    /**
     * Get Open Graph type
     */
    public function getOgType($model = null): string
    {
        if ($model && isset($model->og_type)) {
            return $model->og_type;
        }

        return 'website';
    }

    /**
     * Get Twitter card type
     */
    public function getTwitterCard($model = null): string
    {
        if ($model && $model->twitter_card) {
            return $model->twitter_card;
        }

        return Setting::get('twitter_card_type') ?? 'summary_large_image';
    }

    /**
     * Get Twitter title
     */
    public function getTwitterTitle($model = null): string
    {
        if ($model && $model->twitter_title) {
            return $model->twitter_title;
        }

        return $this->getTitle($model);
    }

    /**
     * Get Twitter description
     */
    public function getTwitterDescription($model = null): string
    {
        if ($model && $model->twitter_description) {
            return $model->twitter_description;
        }

        return $this->getDescription($model);
    }

    /**
     * Get Twitter image
     */
    public function getTwitterImage($model = null): string
    {
        if ($model && $model->twitter_image) {
            return URL::to($model->twitter_image);
        }

        return $this->getOgImage($model);
    }

    /**
     * Generate schema.org JSON-LD markup
     */
    public function getSchemaMarkup($model = null, string $type = 'WebPage'): array
    {
        // If custom schema exists, use it
        if ($model && isset($model->schema_markup) && !empty($model->schema_markup)) {
            return is_array($model->schema_markup) ? $model->schema_markup : json_decode($model->schema_markup, true);
        }

        // Generate default schema based on type
        $schema = [
            '@context' => 'https://schema.org',
            '@type' => $type,
        ];

        // Add common fields
        if ($model) {
            $schema['name'] = $model->title ?? $model->name ?? '';
            $schema['description'] = $this->getDescription($model);
            $schema['url'] = $this->getCanonicalUrl($model);

            // Add image if available
            if (isset($model->image) || isset($model->featured_image)) {
                $schema['image'] = $this->getOgImage($model);
            }

            // Add dates if available
            if (isset($model->published_at)) {
                $schema['datePublished'] = $model->published_at->toIso8601String();
            }
            if (isset($model->updated_at)) {
                $schema['dateModified'] = $model->updated_at->toIso8601String();
            }

            // Add author for articles/posts
            if ($type === 'Article' || $type === 'BlogPosting') {
                if (isset($model->user)) {
                    $schema['author'] = [
                        '@type' => 'Person',
                        'name' => $model->user->name,
                    ];
                }
                $schema['headline'] = $model->title ?? '';
                if (!empty($model->category->name)) {
                    $schema['articleSection'] = $model->category->name;
                }
                $keywords = [];
                if (!empty($model->focus_keyword)) {
                    $keywords[] = $model->focus_keyword;
                }
                if ($model->relationLoaded('tags') || method_exists($model, 'tags')) {
                    foreach ($model->tags as $tag) {
                        $keywords[] = $tag->name;
                    }
                }
                if (!empty($keywords)) {
                    $schema['keywords'] = implode(', ', array_unique($keywords));
                }
                if (!empty($model->content)) {
                    $schema['wordCount'] = str_word_count(strip_tags($model->content));
                }
                if (method_exists($model, 'approvedComments')) {
                    $schema['commentCount'] = $model->approvedComments()->count();
                }
            }

            // Add product-specific fields
            if ($type === 'Product') {
                $schema['offers'] = [
                    '@type' => 'Offer',
                    'price' => $model->price ?? 0,
                    'priceCurrency' => 'USD',
                    'availability' => ($model->is_active ?? true)
                        ? 'https://schema.org/InStock'
                        : 'https://schema.org/OutOfStock',
                ];

                if (isset($model->original_price)) {
                    $schema['offers']['priceValidUntil'] = now()->addYear()->toDateString();
                }

                // AggregateRating from approved reviews (star ratings in SERPs).
                if ($model->relationLoaded('reviews')) {
                    $approved = $model->reviews->where('status', 'approved');
                } else {
                    $approved = $model->reviews()->where('status', 'approved')->get();
                }

                if ($approved->count() > 0) {
                    $schema['aggregateRating'] = [
                        '@type' => 'AggregateRating',
                        'ratingValue' => round($approved->avg('rating'), 1),
                        'reviewCount' => $approved->count(),
                    ];
                }
            }
        }

        // Add organization
        $schema['publisher'] = [
            '@type' => 'Organization',
            'name' => Setting::get('organization_name') ?? Setting::get('site_name') ?? config('app.name'),
            'logo' => [
                '@type' => 'ImageObject',
                'url' => URL::to(Setting::get('organization_logo') ?? Setting::get('logo') ?? '/assets/logo.png'),
            ],
        ];

        // BreadcrumbList alongside the main entity (course rule M27).
        $breadcrumb = $this->getBreadcrumbMarkup($model, $type);

        $graph = [$schema, $breadcrumb];

        // WebSite with SearchAction on the homepage so Google can show a
        // sitelinks search box under the listing.
        if (! $model) {
            $graph[] = [
                '@type' => 'WebSite',
                'name' => Setting::get('site_name') ?? config('app.name'),
                'url' => URL::to('/'),
                'potentialAction' => [
                    '@type' => 'SearchAction',
                    'target' => [
                        '@type' => 'EntryPoint',
                        'urlTemplate' => URL::to('/bundles') . '?q={search_term_string}',
                    ],
                    'query-input' => 'required name=search_term_string',
                ],
            ];
        }

        return [
            '@context' => 'https://schema.org',
            '@graph' => $graph,
        ];
    }

    /**
     * Build a BreadcrumbList trail: Home > section > current page.
     */
    protected function getBreadcrumbMarkup($model = null, string $type = 'WebPage'): array
    {
        $items = [
            [
                '@type' => 'ListItem',
                'position' => 1,
                'name' => 'Home',
                'item' => URL::to('/'),
            ],
        ];

        $position = 2;
        $currentName = $this->getTitle($model);

        if ($model instanceof \App\Models\Product) {
            $items[] = [
                '@type' => 'ListItem',
                'position' => $position++,
                'name' => 'Bundles',
                'item' => URL::to('/bundles'),
            ];
            $currentName = $model->title ?? $currentName;
        } elseif ($model instanceof \App\Models\Post) {
            $items[] = [
                '@type' => 'ListItem',
                'position' => $position++,
                'name' => 'Blog',
                'item' => URL::to('/blog'),
            ];
            $currentName = $model->title ?? $currentName;
        }

        $items[] = [
            '@type' => 'ListItem',
            'position' => $position,
            'name' => $currentName,
        ];

        return [
            '@type' => 'BreadcrumbList',
            'itemListElement' => $items,
        ];
    }

    /**
     * Get all SEO data for a model
     */
    public function getAllSeoData($model = null): array
    {
        return [
            'title' => $this->getTitle($model),
            'description' => $this->getDescription($model),
            'canonical_url' => $this->getCanonicalUrl($model),
            'robots' => $this->getRobotsMeta($model),
            'og' => [
                'title' => $this->getOgTitle($model),
                'description' => $this->getOgDescription($model),
                'image' => $this->getOgImage($model),
                'url' => $this->getCanonicalUrl($model),
                'type' => $this->getOgType($model),
                'site_name' => Setting::get('og_site_name') ?? Setting::get('site_name') ?? config('app.name'),
            ],
            'twitter' => [
                'card' => $this->getTwitterCard($model),
                'site' => Setting::get('twitter_username'),
                'title' => $this->getTwitterTitle($model),
                'description' => $this->getTwitterDescription($model),
                'image' => $this->getTwitterImage($model),
            ],
            'schema' => $this->getSchemaMarkup($model, $this->schemaTypeFor($model)),
        ];
    }
}
