<?php

namespace App\Http\Controllers\NewAdmin;

use App\Http\Controllers\Controller;
use App\Models\Tag;
use Illuminate\Http\Request;

class NewAdminTagController extends Controller
{
    public function index(Request $request)
    {
        $type = $request->get('type', 'all'); // all, product, post
        
        $query = Tag::query();
        
        if ($type === 'product') {
            $query->where('type', 'product');
        } elseif ($type === 'post') {
            $query->where('type', 'post');
        }
        // 'all' shows everything
        
        $tags = $query->latest()->get();
        
        return view('new admin.admin.tags.index', compact('tags', 'type'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:tags,slug',
            'description' => 'nullable|string|max:500',
            'type' => 'required|in:product,post',
            'color' => 'nullable|string|max:7|regex:/^#[0-9A-Fa-f]{6}$/',
            'is_active' => 'nullable|boolean',
        ]);

        $validated['is_active'] = $request->has('is_active') ?: true;

        Tag::create($validated);

        return response()->json(['success' => true, 'message' => 'Tag created successfully']);
    }

    public function update(Request $request, string $id)
    {
        $tag = Tag::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:tags,slug,' . $id,
            'description' => 'nullable|string|max:500',
            'type' => 'required|in:product,post',
            'color' => 'nullable|string|max:7|regex:/^#[0-9A-Fa-f]{6}$/',
            'is_active' => 'nullable|boolean',
        ]);

        $validated['is_active'] = $request->has('is_active') ?: $tag->is_active;

        $tag->update($validated);

        return response()->json(['success' => true, 'message' => 'Tag updated successfully']);
    }

    public function destroy(string $id)
    {
        $tag = Tag::findOrFail($id);
        $tag->delete();

        return redirect()->route('newadmin.tags.index')->with('success', 'Tag deleted successfully');
    }
}
