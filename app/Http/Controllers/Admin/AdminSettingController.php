<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class AdminSettingController extends Controller
{
    public function index()
    {
        $settings = Setting::orderBy('key')->get();
        return view('admin.settings.index', compact('settings'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'key' => 'required|string|max:255|unique:settings,key',
            'value' => 'nullable|string',
        ]);

        Setting::create($validated);

        return redirect()->route('admin.settings.index')->with('success', 'Setting created successfully');
    }

    public function edit(string $key)
    {
        $setting = Setting::where('key', $key)->firstOrFail();
        return view('admin.settings.edit', compact('setting'));
    }

    public function update(Request $request, string $key)
    {
        $setting = Setting::where('key', $key)->firstOrFail();

        $validated = $request->validate([
            'value' => 'nullable|string',
        ]);

        $setting->update($validated);
        Setting::clearCache();

        return redirect()->route('admin.settings.index')->with('success', 'Setting updated successfully');
    }
}
