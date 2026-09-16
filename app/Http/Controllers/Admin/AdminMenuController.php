<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use Illuminate\Http\Request;

class AdminMenuController extends Controller
{
    public function index()
    {
        $menus = Menu::orderBy('sort_order')->latest()->get();
        return view('admin.menus.index', compact('menus'));
    }

    public function create()
    {
        return view('admin.menus.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'label' => 'required|string|max:255',
            'url' => 'nullable|string|max:255',
            'route' => 'nullable|string|max:255',
            'target' => 'nullable|in:_self,_blank',
            'icon' => 'nullable|string|max:255',
            'css_class' => 'nullable|string|max:255',
            'sort_order' => 'nullable|integer',
            'is_active' => 'nullable|boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');
        $validated['sort_order'] = $validated['sort_order'] ?? 0;
        $validated['target'] = $validated['target'] ?? '_self';

        Menu::create($validated);

        return redirect()->route('admin.menus.index')->with('success', 'Menu item created successfully');
    }

    public function edit(string $id)
    {
        $menu = Menu::findOrFail($id);
        return view('admin.menus.edit', compact('menu'));
    }

    public function update(Request $request, string $id)
    {
        $menu = Menu::findOrFail($id);

        $validated = $request->validate([
            'label' => 'required|string|max:255',
            'url' => 'nullable|string|max:255',
            'route' => 'nullable|string|max:255',
            'target' => 'nullable|in:_self,_blank',
            'icon' => 'nullable|string|max:255',
            'css_class' => 'nullable|string|max:255',
            'sort_order' => 'nullable|integer',
            'is_active' => 'nullable|boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');
        $validated['target'] = $validated['target'] ?? '_self';

        $menu->update($validated);

        return redirect()->route('admin.menus.index')->with('success', 'Menu item updated successfully');
    }

    public function destroy(string $id)
    {
        $menu = Menu::findOrFail($id);
        $menu->delete();

        return redirect()->route('admin.menus.index')->with('success', 'Menu item deleted successfully');
    }
}
