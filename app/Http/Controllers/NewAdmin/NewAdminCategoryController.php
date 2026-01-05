<?php

namespace App\Http\Controllers\NewAdmin;

use App\Http\Controllers\Controller;
use App\Models\PostCategory;
use Illuminate\Http\Request;

class NewAdminCategoryController extends Controller
{
    public function index()
    {
        $categories = PostCategory::latest()->get();
        return view('new admin.admin.categories.index', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:post_categories,slug',
            'description' => 'nullable|string',
        ]);

        PostCategory::create($validated);

        return response()->json(['success' => true, 'message' => 'Category created successfully']);
    }

    public function update(Request $request, string $id)
    {
        $category = PostCategory::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:post_categories,slug,' . $id,
            'description' => 'nullable|string',
        ]);

        $category->update($validated);

        return response()->json(['success' => true, 'message' => 'Category updated successfully']);
    }

    public function destroy(string $id)
    {
        $category = PostCategory::findOrFail($id);
        $category->delete();

        return redirect()->route('newadmin.categories.index')->with('success', 'Category deleted successfully');
    }
}
