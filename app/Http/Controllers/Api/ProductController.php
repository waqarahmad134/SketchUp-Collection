<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Product::where('is_active', true);

        // Filter by category
        if ($request->has('category')) {
            $query->where('category', $request->category);
        }

        // Filter by bundle
        if ($request->has('is_bundle')) {
            $query->where('is_bundle', $request->boolean('is_bundle'));
        }

        // Search
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $products = $query->orderBy('sort_order')
                         ->orderBy('created_at', 'desc')
                         ->paginate($request->get('per_page', 15));

        return response()->json($products);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $product = Product::where('is_active', true)
                         ->where(function ($query) use ($id) {
                             $query->where('id', $id)
                                   ->orWhere('slug', $id);
                         })
                         ->firstOrFail();

        return response()->json($product);
    }
}
