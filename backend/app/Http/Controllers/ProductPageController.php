<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProductPageController extends Controller
{
    public function index(): View
    {
        $products = Product::where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        return view('bundles.index', [
            'title' => 'Products & Bundles - 3DAssetHub',
            'metaDescription' => 'Browse premium 3D assets and bundles for designers.',
            'products' => $products,
        ]);
    }

    public function show(string $slug): View
    {
        $product = Product::where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();

        $allProducts = Product::where('is_active', true)->get()->keyBy('id');

        return view('bundles.show', [
            'title' => $product->title . ' - 3DAssetHub',
            'metaDescription' => $product->full_description ?? $product->description ?? '',
            'product' => $product,
            'allProducts' => $allProducts,
        ]);
    }
}

