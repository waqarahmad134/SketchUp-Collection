<?php

namespace Database\Seeders;

use App\Models\Tag;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class TagSeeder extends Seeder
{
    public function run(): void
    {
        $productTags = [
            '3d models',
            'bundles',
            'free',
            'pbr',
            'textures',
            'furniture',
            'architecture',
            'interior',
            'exterior',
            'render-ready',
        ];

        $postTags = [
            'news',
            'release',
            'tutorial',
            'workflow',
            'updates',
            'community',
            'tips',
        ];

        foreach ($productTags as $name) {
            Tag::updateOrCreate(
                ['slug' => Str::slug($name)],
                [
                    'name' => ucwords($name),
                    'type' => 'product',
                    'is_active' => true,
                ]
            );
        }

        foreach ($postTags as $name) {
            Tag::updateOrCreate(
                ['slug' => Str::slug($name . '-post')],
                [
                    'name' => ucwords($name),
                    'type' => 'post',
                    'is_active' => true,
                ]
            );
        }
    }
}

