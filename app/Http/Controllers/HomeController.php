<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Product;

class HomeController extends Controller
{
    public function index()
    {
        $products = Product::where('is_active', true)
            ->orderBy('sort_order')
            ->take(3)
            ->get();

        $featuredPosts = Post::with('user')
            ->where('status', 'published')
            ->where(function ($query) {
                $query->whereNull('published_at')
                    ->orWhere('published_at', '<=', now());
            })
            ->where('is_featured', true)
            ->latest('published_at')
            ->take(3)
            ->get();

        if ($featuredPosts->isEmpty()) {
            $featuredPosts = Post::with('user')
                ->where('status', 'published')
                ->where(function ($query) {
                    $query->whereNull('published_at')
                        ->orWhere('published_at', '<=', now());
                })
                ->latest('published_at')
                ->take(3)
                ->get();
        }

        return view('home', [
            'title' => 'SketchUp Collection | Premium 3D Assets for Designers',
            'metaDescription' => 'Premium SketchUp model bundle collection with high quality exterior, interior, landscape and misc 3D models. Ready to use for architects and designers.',
            'products' => $products,
            'featuredPosts' => $featuredPosts,
        ]);
    }
}

