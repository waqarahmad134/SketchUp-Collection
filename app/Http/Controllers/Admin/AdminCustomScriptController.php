<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CustomScript;
use Illuminate\Http\Request;

class AdminCustomScriptController extends Controller
{
    public function index()
    {
        $scripts = CustomScript::orderBy('sort_order')->latest()->get();
        return view('admin.custom-scripts.index', compact('scripts'));
    }

    public function create()
    {
        return view('admin.custom-scripts.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'position' => 'required|in:head,body_start,body_end',
            'code' => 'required|string',
            'is_active' => 'nullable|boolean',
            'sort_order' => 'nullable|integer',
        ]);

        $validated['is_active'] = $request->has('is_active');
        $validated['sort_order'] = $validated['sort_order'] ?? 0;

        CustomScript::create($validated);

        return redirect()->route('admin.custom-scripts.index')->with('success', 'Custom script created successfully');
    }

    public function edit(string $id)
    {
        $script = CustomScript::findOrFail($id);
        return view('admin.custom-scripts.edit', compact('script'));
    }

    public function update(Request $request, string $id)
    {
        $script = CustomScript::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'position' => 'required|in:head,body_start,body_end',
            'code' => 'required|string',
            'is_active' => 'nullable|boolean',
            'sort_order' => 'nullable|integer',
        ]);

        $validated['is_active'] = $request->has('is_active');

        $script->update($validated);

        return redirect()->route('admin.custom-scripts.index')->with('success', 'Custom script updated successfully');
    }

    public function destroy(string $id)
    {
        $script = CustomScript::findOrFail($id);
        $script->delete();

        return redirect()->route('admin.custom-scripts.index')->with('success', 'Custom script deleted successfully');
    }
}
