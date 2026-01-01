<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Post;
use App\Models\ProductCategory;
use App\Models\PostCategory;
use Spatie\Sitemap\Sitemap;
use Spatie\Sitemap\Tags\Url;

class SitemapController extends Controller
{
    public function index()
    {
        $sitemap = Sitemap::create();

        // Add homepage
        $sitemap->add(
            Url::create('/')
                ->setLastModificationDate(now())
                ->setChangeFrequency(Url::CHANGE_FREQUENCY_DAILY)
                ->setPriority(1.0)
        );

        // Add products
        Product::where('is_active', true)->get()->each(function (Product $product) use ($sitemap) {
            $sitemap->add(
                Url::create("/bundles/{$product->slug}")
                    ->setLastModificationDate($product->updated_at)
                    ->setChangeFrequency(Url::CHANGE_FREQUENCY_WEEKLY)
                    ->setPriority(0.8)
            );
        });

        // Add posts
        Post::where('status', 'published')->get()->each(function (Post $post) use ($sitemap) {
            $sitemap->add(
                Url::create("/blog/{$post->slug}")
                    ->setLastModificationDate($post->updated_at)
                    ->setChangeFrequency(Url::CHANGE_FREQUENCY_WEEKLY)
                    ->setPriority(0.7)
            );
        });

        // Add product categories
        // ProductCategory::where('is_active', true)->get()->each(function (ProductCategory $category) use ($sitemap) {
        //     $sitemap->add(
        //         Url::create("/categories/{$category->slug}")
        //             ->setLastModificationDate($category->updated_at)
        //             ->setChangeFrequency(Url::CHANGE_FREQUENCY_WEEKLY)
        //             ->setPriority(0.6)
        //     );
        // });

        // Add static pages
        $staticPages = [
            '/about' => 0.5,
            '/contact' => 0.5,
            '/terms-of-service' => 0.5,
            '/privacy-policy' => 0.5,
            '/pricing' => 0.7,
            '/bundles' => 0.9,
        ];

        foreach ($staticPages as $url => $priority) {
            $sitemap->add(
                Url::create($url)
                    ->setLastModificationDate(now())
                    ->setChangeFrequency(Url::CHANGE_FREQUENCY_MONTHLY)
                    ->setPriority($priority)
            );
        }

        return $sitemap->toResponse(request());
    }
}

