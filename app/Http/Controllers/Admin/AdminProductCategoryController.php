<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProductCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AdminProductCategoryController extends Controller
{
    public function index()
    {
        $categories = ProductCategory::with('parent')->latest()->get();
        return view('admin.product-categories.index', compact('categories'));
    }

    public function create()
    {
        $parentCategories = ProductCategory::whereNull('parent_id')->where('is_active', true)->get();
        return view('admin.product-categories.create', compact('parentCategories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:product_categories,slug',
            'description' => 'nullable|string',
            'parent_id' => 'nullable|exists:product_categories,id',
            'image' => 'nullable|image|max:2048',
            'icon' => 'nullable|string|max:255',
            'color' => 'nullable|string|max:7',
            'is_active' => 'nullable|boolean',
            'sort_order' => 'nullable|integer',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:255',
        ]);

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('product-categories', 'public');
        }

        $validated['is_active'] = $request->has('is_active');
        $validated['sort_order'] = $validated['sort_order'] ?? 0;

        ProductCategory::create($validated);

        return redirect()->route('admin.product-categories.index')->with('success', 'Product category created successfully');
    }

    public function edit(string $id)
    {
        $category = ProductCategory::findOrFail($id);
        $parentCategories = ProductCategory::whereNull('parent_id')->where('is_active', true)->where('id', '!=', $id)->get();
        return view('admin.product-categories.edit', compact('category', 'parentCategories'));
    }

    public function update(Request $request, string $id)
    {
        $category = ProductCategory::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:product_categories,slug,' . $id,
            'description' => 'nullable|string',
            'parent_id' => 'nullable|exists:product_categories,id|not_in:' . $id,
            'image' => 'nullable|image|max:2048',
            'icon' => 'nullable|string|max:255',
            'color' => 'nullable|string|max:7',
            'is_active' => 'nullable|boolean',
            'sort_order' => 'nullable|integer',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:255',
        ]);

        if ($request->hasFile('image')) {
            if ($category->image) {
                Storage::disk('public')->delete($category->image);
            }
            $validated['image'] = $request->file('image')->store('product-categories', 'public');
        }

        $validated['is_active'] = $request->has('is_active');

        $category->update($validated);

        return redirect()->route('admin.product-categories.index')->with('success', 'Product category updated successfully');
    }

    public function destroy(string $id)
    {
        $category = ProductCategory::findOrFail($id);
        
        if ($category->products()->count() > 0) {
            return redirect()->route('admin.product-categories.index')->with('error', 'Cannot delete category with products. Please remove products first.');
        }

        if ($category->image) {
            Storage::disk('public')->delete($category->image);
        }

        $category->delete();

        return redirect()->route('admin.product-categories.index')->with('success', 'Product category deleted successfully');
    }
}
