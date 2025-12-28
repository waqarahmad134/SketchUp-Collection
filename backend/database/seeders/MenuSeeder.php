<?php

namespace Database\Seeders;

use App\Models\Menu;
use Illuminate\Database\Seeder;

class MenuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $menuItems = [
            [
                'label' => 'Home',
                'url' => '/',
                'route' => null,
                'target' => '_self',
                'icon' => 'heroicon-o-home',
                'css_class' => null,
                'sort_order' => 1,
                'is_active' => true,
            ],
            [
                'label' => 'Bundles',
                'url' => '/bundles',
                'route' => null,
                'target' => '_self',
                'icon' => 'heroicon-o-cube',
                'css_class' => null,
                'sort_order' => 2,
                'is_active' => true,
            ],
            [
                'label' => 'Features',
                'url' => '/#features',
                'route' => null,
                'target' => '_self',
                'icon' => 'heroicon-o-sparkles',
                'css_class' => null,
                'sort_order' => 3,
                'is_active' => true,
            ],
            [
                'label' => 'Testimonials',
                'url' => '/#testimonials',
                'route' => null,
                'target' => '_self',
                'icon' => 'heroicon-o-chat-bubble-left-right',
                'css_class' => null,
                'sort_order' => 4,
                'is_active' => true,
            ],
            [
                'label' => 'Blog',
                'url' => '/blog',
                'route' => null,
                'target' => '_self',
                'icon' => 'heroicon-o-document-text',
                'css_class' => null,
                'sort_order' => 5,
                'is_active' => true,
            ],
        ];

        foreach ($menuItems as $item) {
            Menu::updateOrCreate(
                ['label' => $item['label']], // Find by label
                $item // Update or create with these values
            );
        }
    }
}

