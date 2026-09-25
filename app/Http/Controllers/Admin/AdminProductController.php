<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\Tag;
use Illuminate\Http\Request;
use App\Support\ImageOptimizer;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AdminProductController extends Controller
{
    public function index()
    {
        $products = Product::with(['category', 'tags'])->latest()->get();
        return view('admin.products.index', compact('products'));
    }

    public function create()
    {
        $categories = ProductCategory::where('is_active', true)->get();
        $tags = Tag::where(function($q) {
            $q->where('type', 'product')->orWhereNull('type');
        })->where('is_active', true)->get();
        $allProducts = Product::where('is_active', true)->get();
        return view('admin.products.create', compact('categories', 'tags', 'allProducts'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:products,slug',
            'description' => 'required|string|max:500',
            'full_description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'original_price' => 'required|numeric|min:0',
            'category_id' => 'nullable|exists:product_categories,id',
            'is_bundle' => 'nullable|boolean',
            'is_digital' => 'nullable|boolean',
            'is_active' => 'nullable|boolean',
            'file_count' => 'nullable|integer',
            'file_size' => 'nullable|string|max:255',
            'sketchup_version' => 'nullable|string|max:255',
            'image_alt' => 'nullable|string|max:255',
            'sort_order' => 'nullable|integer',
            'image' => 'required|image|max:5120',
            'images' => 'nullable|array',
            'images.*' => 'image|max:5120',
            'download_file' => 'nullable|file|max:51200',
            'sample_file' => 'nullable|file|max:51200',
            'download_links' => 'nullable|string',
            'features' => 'nullable|string',
            'tags' => 'nullable|array',
            'tags.*' => 'exists:tags,id',
            'included_products' => [
                'nullable',
                'array',
                function ($attribute, $value, $fail) use ($request) {
                    // If bundle is checked, require at least one included product
                    if ($request->has('is_bundle') && (empty($value) || count($value) === 0)) {
                        $fail('A bundle must include at least one product.');
                    }
                },
            ],
            'included_products.*' => [
                'exists:products,id',
                function ($attribute, $value, $fail) {
                    // Ensure included products are active
                    $product = Product::find($value);
                    if ($product && !$product->is_active) {
                        $fail('Included products must be active.');
                    }
                },
            ],
            // SEO fields
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'canonical_url' => 'nullable|url|max:255',
            'robots_index' => 'nullable|in:index,noindex',
            'robots_follow' => 'nullable|in:follow,nofollow',
            'og_title' => 'nullable|string|max:255',
            'og_description' => 'nullable|string|max:500',
            'og_image' => 'nullable|image|max:5120',
            'og_type' => 'nullable|in:product,website,article',
            'twitter_card' => 'nullable|in:summary,summary_large_image,player,app',
            'twitter_title' => 'nullable|string|max:255',
            'twitter_description' => 'nullable|string|max:500',
            'twitter_image' => 'nullable|image|max:5120',
            'schema_markup' => 'nullable|string',
        ]);

        // Handle file uploads
        if ($request->hasFile('image')) {
            $validated['image'] = ImageOptimizer::optimize($request->file('image'), 'products');
        }

        if ($request->hasFile('images')) {
            $imagePaths = [];
            foreach ($request->file('images') as $image) {
                $imagePaths[] = ImageOptimizer::optimize($image, 'products/gallery');
            }
            $validated['images'] = $imagePaths;
        }

        if ($request->hasFile('download_file')) {
            $validated['download_file'] = $request->file('download_file')->store('products/downloads', 'private');
        }

        if ($request->hasFile('sample_file')) {
            $validated['sample_file'] = $request->file('sample_file')->store('products/samples', 'private');
        }

        if ($request->hasFile('og_image')) {
            $validated['og_image'] = ImageOptimizer::optimize($request->file('og_image'), 'seo/og', 1200);
        }

        if ($request->hasFile('twitter_image')) {
            $validated['twitter_image'] = ImageOptimizer::optimize($request->file('twitter_image'), 'seo/twitter', 1200);
        }

        // Handle JSON fields
        if (isset($validated['features']) && is_string($validated['features'])) {
            $validated['features'] = json_decode($validated['features'], true) ?? [];
        }
        if (isset($validated['download_links']) && is_string($validated['download_links'])) {
            $validated['download_links'] = json_decode($validated['download_links'], true) ?? [];
        }
        if (isset($validated['included_products'])) {
            if (is_string($validated['included_products'])) {
                $validated['included_products'] = json_decode($validated['included_products'], true) ?? [];
            }
        } else {
            $validated['included_products'] = [];
        }
        
        // Handle boolean fields
        $validated['is_bundle'] = $request->has('is_bundle');
        $validated['is_digital'] = $request->has('is_digital') ?: true;
        $validated['is_active'] = $request->has('is_active') ?: true;
        $validated['user_id'] = auth()->id();
        $validated['sort_order'] = $validated['sort_order'] ?? 0;
        $validated['file_count'] = $validated['file_count'] ?? 0;

        // Store tags separately (many-to-many)
        $tags = $validated['tags'] ?? [];
        unset($validated['tags']);

        $product = Product::create($validated);

        // Attach tags
        if (!empty($tags)) {
            $product->tags()->attach($tags);
        }

        return redirect()->route('admin.products.index')->with('success', 'Product created successfully');
    }

    public function edit(string $id)
    {
        $product = Product::with(['category', 'tags'])->findOrFail($id);
        $categories = ProductCategory::where('is_active', true)->get();
        $tags = Tag::where('type', 'product')->orWhereNull('type')->where('is_active', true)->get();
        $allProducts = Product::where('is_active', true)->where('id', '!=', $id)->get();
        return view('admin.products.edit', compact('product', 'categories', 'tags', 'allProducts'));
    }

    public function update(Request $request, string $id)
    {
        $product = Product::findOrFail($id);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:products,slug,' . $id,
            'description' => 'required|string|max:500',
            'full_description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'original_price' => 'required|numeric|min:0',
            'category_id' => 'nullable|exists:product_categories,id',
            'is_bundle' => 'nullable|boolean',
            'is_digital' => 'nullable|boolean',
            'is_active' => 'nullable|boolean',
            'file_count' => 'nullable|integer',
            'file_size' => 'nullable|string|max:255',
            'sketchup_version' => 'nullable|string|max:255',
            'image_alt' => 'nullable|string|max:255',
            'sort_order' => 'nullable|integer',
            'image' => 'nullable|image|max:5120',
            'images' => 'nullable|array',
            'images.*' => 'image|max:5120',
            'download_file' => 'nullable|file|max:51200',
            'sample_file' => 'nullable|file|max:51200',
            'download_links' => 'nullable|string',
            'features' => 'nullable|string',
            'tags' => 'nullable|array',
            'tags.*' => 'exists:tags,id',
            'included_products' => [
                'nullable',
                'array',
                function ($attribute, $value, $fail) use ($request) {
                    // If bundle is checked, require at least one included product
                    if ($request->has('is_bundle') && (empty($value) || count($value) === 0)) {
                        $fail('A bundle must include at least one product.');
                    }
                },
            ],
            'included_products.*' => [
                'exists:products,id',
                function ($attribute, $value, $fail) use ($id) {
                    // Prevent self-inclusion
                    if ($value == $id) {
                        $fail('A bundle cannot include itself.');
                    }
                    // Ensure included products are active
                    $includedProduct = Product::find($value);
                    if ($includedProduct && !$includedProduct->is_active) {
                        $fail('Included products must be active.');
                    }
                },
            ],
            // SEO fields
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'canonical_url' => 'nullable|url|max:255',
            'robots_index' => 'nullable|in:index,noindex',
            'robots_follow' => 'nullable|in:follow,nofollow',
            'og_title' => 'nullable|string|max:255',
            'og_description' => 'nullable|string|max:500',
            'og_image' => 'nullable|image|max:5120',
            'og_type' => 'nullable|in:product,website,article',
            'twitter_card' => 'nullable|in:summary,summary_large_image,player,app',
            'twitter_title' => 'nullable|string|max:255',
            'twitter_description' => 'nullable|string|max:500',
            'twitter_image' => 'nullable|image|max:5120',
            'schema_markup' => 'nullable|string',
        ]);

        // Handle file uploads
        if ($request->hasFile('image')) {
            if ($product->image) {
                Storage::disk('public')->delete($product->image);
            }
            $validated['image'] = ImageOptimizer::optimize($request->file('image'), 'products');
        }

        if ($request->hasFile('images')) {
            if ($product->images) {
                foreach ($product->images as $oldImage) {
                    Storage::disk('public')->delete($oldImage);
                }
            }
            $imagePaths = [];
            foreach ($request->file('images') as $image) {
                $imagePaths[] = ImageOptimizer::optimize($image, 'products/gallery');
            }
            $validated['images'] = $imagePaths;
        }

        if ($request->hasFile('download_file')) {
            if ($product->download_file) {
                Storage::disk('private')->delete($product->download_file);
            }
            $validated['download_file'] = $request->file('download_file')->store('products/downloads', 'private');
        }

        if ($request->hasFile('sample_file')) {
            if ($product->sample_file) {
                Storage::disk('private')->delete($product->sample_file);
            }
            $validated['sample_file'] = $request->file('sample_file')->store('products/samples', 'private');
        }

        if ($request->hasFile('og_image')) {
            if ($product->og_image) {
                Storage::disk('public')->delete($product->og_image);
            }
            $validated['og_image'] = ImageOptimizer::optimize($request->file('og_image'), 'seo/og', 1200);
        }

        if ($request->hasFile('twitter_image')) {
            if ($product->twitter_image) {
                Storage::disk('public')->delete($product->twitter_image);
            }
            $validated['twitter_image'] = ImageOptimizer::optimize($request->file('twitter_image'), 'seo/twitter', 1200);
        }

        // Handle JSON fields
        if (isset($validated['features']) && is_string($validated['features'])) {
            $validated['features'] = json_decode($validated['features'], true) ?? [];
        }
        if (isset($validated['download_links']) && is_string($validated['download_links'])) {
            $validated['download_links'] = json_decode($validated['download_links'], true) ?? [];
        }
        if (isset($validated['included_products'])) {
            if (is_string($validated['included_products'])) {
                $validated['included_products'] = json_decode($validated['included_products'], true) ?? [];
            }
            // Remove duplicates, ensure array of integers, and remove self if present
            $validated['included_products'] = array_unique(array_map('intval', $validated['included_products']));
            $validated['included_products'] = array_values(array_filter($validated['included_products'], function($pid) use ($id) {
                return $pid != $id;
            }));
        } else {
            $validated['included_products'] = [];
        }
        
        // Handle boolean fields
        $validated['is_bundle'] = $request->has('is_bundle');
        $validated['is_digital'] = $request->has('is_digital') ?: true;
        $validated['is_active'] = $request->has('is_active') ?: true;
        
        // If not a bundle, clear included products
        if (!$validated['is_bundle']) {
            $validated['included_products'] = [];
        }

        // Store tags separately
        $tags = $validated['tags'] ?? [];
        unset($validated['tags']);

        $product->update($validated);

        // Sync tags
        $product->tags()->sync($tags);

        return redirect()->route('admin.products.index')->with('success', 'Product updated successfully');
    }

    public function destroy(string $id)
    {
        $product = Product::findOrFail($id);

        // Delete files
        if ($product->image) {
            Storage::disk('public')->delete($product->image);
        }
        if ($product->images) {
            foreach ($product->images as $image) {
                Storage::disk('public')->delete($image);
            }
        }
        if ($product->download_file) {
            Storage::disk('private')->delete($product->download_file);
        }
        if ($product->og_image) {
            Storage::disk('public')->delete($product->og_image);
        }
        if ($product->twitter_image) {
            Storage::disk('public')->delete($product->twitter_image);
        }

        $product->delete();

        return redirect()->route('admin.products.index')->with('success', 'Product deleted successfully');
    }
}
