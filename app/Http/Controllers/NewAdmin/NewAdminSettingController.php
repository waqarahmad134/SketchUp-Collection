<?php

namespace App\Http\Controllers\NewAdmin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class NewAdminSettingController extends Controller
{
    public function index()
    {
        $settings = Setting::orderBy('key')->get();
        return view('new admin.admin.settings.index', compact('settings'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'key' => 'required|string|max:255|unique:settings,key',
            'value' => 'nullable|string',
        ]);

        Setting::create($validated);

        return redirect()->route('newadmin.settings.index')->with('success', 'Setting created successfully');
    }

    public function edit(string $key)
    {
        $setting = Setting::where('key', $key)->firstOrFail();
        return view('new admin.admin.settings.edit', compact('setting'));
    }

    public function update(Request $request, string $key)
    {
        $setting = Setting::where('key', $key)->firstOrFail();

        $validated = $request->validate([
            'value' => 'nullable|string',
        ]);

        $setting->update($validated);
        Setting::clearCache();

        return redirect()->route('newadmin.settings.index')->with('success', 'Setting updated successfully');
    }
}
