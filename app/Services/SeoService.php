<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\Facades\URL;

class SeoService
{
    /**
     * Generate meta title
     */
    public function getTitle($model = null): string
    {
        if ($model && $model->meta_title) {
            return $model->meta_title;
        }

        if ($model && isset($model->title)) {
            $siteName = Setting::get('site_name') ?? config('app.name');
            return $model->title . ' - ' . $siteName;
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
            }

            // Add product-specific fields
            if ($type === 'Product') {
                $schema['offers'] = [
                    '@type' => 'Offer',
                    'price' => $model->price ?? 0,
                    'priceCurrency' => 'USD',
                    'availability' => 'https://schema.org/InStock',
                ];

                if (isset($model->original_price)) {
                    $schema['offers']['priceValidUntil'] = now()->addYear()->toDateString();
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

        return $schema;
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
            'schema' => $this->getSchemaMarkup($model),
        ];
    }
}
