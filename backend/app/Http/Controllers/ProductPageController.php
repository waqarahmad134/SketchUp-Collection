<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProductPageController extends Controller
{
    public function index(Request $request): View
    {
        $query = Product::where('is_active', true)
            ->with(['category', 'tags']);

        // Category filter
        if ($request->filled('category')) {
            $categoryIds = is_array($request->category) 
                ? array_filter($request->category) 
                : (array) $request->category;
            if (!empty($categoryIds)) {
                $query->whereIn('category_id', $categoryIds);
            }
        }

        // Tag filter
        if ($request->filled('tags')) {
            $tagIds = is_array($request->tags) 
                ? array_filter($request->tags) 
                : (array) $request->tags;
            if (!empty($tagIds)) {
                $query->whereHas('tags', function ($q) use ($tagIds) {
                    $q->whereIn('tags.id', $tagIds);
                });
            }
        }

        // Bundle/Product type filter
        if ($request->filled('type')) {
            if ($request->type === 'bundle') {
                $query->where('is_bundle', true);
            } elseif ($request->type === 'product') {
                $query->where('is_bundle', false);
            }
        }

        // Price range filter
        if ($request->filled('min_price')) {
            $query->where('price', '>=', $request->min_price);
        }
        if ($request->filled('max_price')) {
            $query->where('price', '<=', $request->max_price);
        }

        // Sort order
        $sortBy = $request->get('sort', 'default');
        switch ($sortBy) {
            case 'price_low':
                $query->orderBy('price', 'asc');
                break;
            case 'price_high':
                $query->orderBy('price', 'desc');
                break;
            case 'newest':
                $query->orderBy('created_at', 'desc');
                break;
            case 'oldest':
                $query->orderBy('created_at', 'asc');
                break;
            default:
                $query->orderBy('sort_order')->orderBy('created_at', 'desc');
                break;
        }

        // Pagination
        $perPage = $request->get('per_page', 12);
        $perPage = in_array($perPage, [9, 12, 18, 24, 36]) ? $perPage : 12;
        $products = $query->paginate($perPage)->withQueryString();

        // Get filter options
        $categories = ProductCategory::active()->orderBy('sort_order')->get();
        $tags = Tag::active()->productTags()->orderBy('name')->get();
        
        // Get price range for filter
        $minPrice = Product::where('is_active', true)->min('price') ?? 0;
        $maxPrice = Product::where('is_active', true)->max('price') ?? 1000;

        // Normalize filter values for view
        $filters = [
            'category' => is_array($request->category) ? $request->category : ($request->category ? [$request->category] : []),
            'tags' => is_array($request->tags) ? $request->tags : ($request->tags ? [$request->tags] : []),
            'type' => $request->get('type', ''),
            'min_price' => $request->get('min_price', ''),
            'max_price' => $request->get('max_price', ''),
            'sort' => $request->get('sort', 'default'),
            'per_page' => $perPage,
            'view' => $request->get('view', 'grid'),
            'columns' => $request->get('columns', '3'),
        ];

        // If AJAX request, return JSON
        if ($request->ajax()) {
            return response()->json([
                'products' => $products->items(),
                'pagination' => [
                    'current_page' => $products->currentPage(),
                    'last_page' => $products->lastPage(),
                    'per_page' => $products->perPage(),
                    'total' => $products->total(),
                    'from' => $products->firstItem(),
                    'to' => $products->lastItem(),
                    'has_more' => $products->hasMorePages(),
                    'prev_url' => $products->previousPageUrl(),
                    'next_url' => $products->nextPageUrl(),
                ],
            ]);
        }

        return view('bundles.index', [
            'title' => 'Products & Bundles - SketchUp Collection',
            'metaDescription' => 'Browse premium 3D assets and bundles for designers.',
            'products' => $products,
            'categories' => $categories,
            'tags' => $tags,
            'minPrice' => $minPrice,
            'maxPrice' => $maxPrice,
            'filters' => $filters,
        ]);
    }

    public function show(string $slug): View
    {
        $product = Product::with(['user', 'reviews' => function ($q) {
            $q->where('status', 'approved')->latest();
        }])
            ->where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();

        $allProducts = Product::where('is_active', true)->get()->keyBy('id');

        return view('bundles.show', [
            'title' => $product->title . ' - SketchUp Collection',
            'metaDescription' => $product->full_description ?? $product->description ?? '',
            'product' => $product,
            'allProducts' => $allProducts,
        ]);
    }
}

