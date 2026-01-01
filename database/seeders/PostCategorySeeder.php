<?php

namespace Database\Seeders;

use App\Models\PostCategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class PostCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'News', 'description' => 'Product updates and announcements', 'sort_order' => 1],
            ['name' => 'Tutorials', 'description' => 'How-to guides and walkthroughs', 'sort_order' => 2],
            ['name' => 'Inspiration', 'description' => 'Showcases and community highlights', 'sort_order' => 3],
            ['name' => 'Tips & Tricks', 'description' => 'Quick wins and productivity tips', 'sort_order' => 4],
            ['name' => 'Release Notes', 'description' => 'Changelog and release details', 'sort_order' => 5],
        ];

        foreach ($categories as $cat) {
            PostCategory::updateOrCreate(
                ['slug' => Str::slug($cat['name'])],
                [
                    'name' => $cat['name'],
                    'description' => $cat['description'] ?? null,
                    'sort_order' => $cat['sort_order'] ?? 0,
                    'is_active' => true,
                ]
            );
        }
    }
}

