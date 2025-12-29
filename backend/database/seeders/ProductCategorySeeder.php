<?php

namespace Database\Seeders;

use App\Models\ProductCategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProductCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Bundles', 'description' => 'Curated bundles of 3D assets', 'sort_order' => 1],
            ['name' => 'Free Assets', 'description' => 'Free high-quality assets', 'sort_order' => 2],
            ['name' => 'Interiors', 'description' => 'Interior design assets', 'sort_order' => 3],
            ['name' => 'Exteriors', 'description' => 'Exterior and landscape assets', 'sort_order' => 4],
            ['name' => 'Furniture', 'description' => 'Furniture and decor', 'sort_order' => 5],
            ['name' => 'Textures', 'description' => 'Materials and textures', 'sort_order' => 6],
        ];

        foreach ($categories as $cat) {
            ProductCategory::updateOrCreate(
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

