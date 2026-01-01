<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SeoSettingsSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            // Basic SEO
            ['key' => 'site_name', 'value' => 'SketchUp Collection'],
            ['key' => 'site_tagline', 'value' => 'Premium 3D Assets for Designers'],
            ['key' => 'default_meta_title', 'value' => 'SketchUp Collection - Premium 3D Assets'],
            ['key' => 'default_meta_description', 'value' => 'Download premium SketchUp 3D models, furniture, textures, and complete asset bundles. Professional quality resources for architects and designers.'],
            
            // Robots & Indexing
            ['key' => 'robots_index', 'value' => 'index'],
            ['key' => 'robots_follow', 'value' => 'follow'],
            
            // Social Media
            ['key' => 'twitter_username', 'value' => '@sketchupcollection'],
            ['key' => 'facebook_app_id', 'value' => ''],
            
            // Open Graph Defaults
            ['key' => 'og_default_image', 'value' => '/assets/og-default.jpg'],
            ['key' => 'og_site_name', 'value' => 'SketchUp Collection'],
            
            // Twitter Card Defaults
            ['key' => 'twitter_card_type', 'value' => 'summary_large_image'],
            ['key' => 'twitter_default_image', 'value' => '/assets/twitter-default.jpg'],
            
            // Favicon & Icons
            ['key' => 'favicon', 'value' => '/favicon.ico'],
            ['key' => 'apple_touch_icon', 'value' => '/apple-touch-icon.png'],
            ['key' => 'favicon_32', 'value' => '/favicon-32x32.png'],
            ['key' => 'favicon_16', 'value' => '/favicon-16x16.png'],
            ['key' => 'site_manifest', 'value' => '/site.webmanifest'],
            
            // Logo
            ['key' => 'logo', 'value' => '/assets/logo.svg'],
            ['key' => 'logo_dark', 'value' => '/assets/logo-dark.svg'],
            
            // Schema.org
            ['key' => 'organization_name', 'value' => 'SketchUp Collection'],
            ['key' => 'organization_logo', 'value' => '/assets/logo.png'],
            ['key' => 'organization_url', 'value' => config('app.url')],
            
            // Google Services
            ['key' => 'google_analytics_id', 'value' => ''],
            ['key' => 'google_tag_manager_id', 'value' => ''],
            ['key' => 'google_site_verification', 'value' => ''],
            
            // Other Services
            ['key' => 'facebook_pixel_id', 'value' => ''],
            ['key' => 'hotjar_id', 'value' => ''],
        ];

        foreach ($settings as $setting) {
            DB::table('settings')->updateOrInsert(
                ['key' => $setting['key']],
                array_merge($setting, [
                    'created_at' => now(),
                    'updated_at' => now(),
                ])
            );
        }
    }
}
