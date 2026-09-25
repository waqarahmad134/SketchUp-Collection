<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Redirect;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminRedirectController extends Controller
{
    public function index(): View
    {
        $redirects = Redirect::latest()->paginate(25);

        return view('admin.redirects.index', compact('redirects'));
    }

    public function create(): View
    {
        return view('admin.redirects.form', ['redirect' => new Redirect()]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);

        Redirect::create($data);

        return redirect()->route('admin.redirects.index')->with('status', 'Redirect created.');
    }

    public function edit(Redirect $redirect): View
    {
        return view('admin.redirects.form', compact('redirect'));
    }

    public function update(Request $request, Redirect $redirect): RedirectResponse
    {
        $redirect->update($this->validated($request, $redirect->id));

        return redirect()->route('admin.redirects.index')->with('status', 'Redirect updated.');
    }

    public function destroy(Redirect $redirect): RedirectResponse
    {
        $redirect->delete();

        return back()->with('status', 'Redirect deleted.');
    }

    protected function validated(Request $request, ?int $ignoreId = null): array
    {
        $data = $request->validate([
            'from_path' => ['required', 'string', 'max:255', 'unique:redirects,from_path' . ($ignoreId ? ",{$ignoreId}" : '')],
            'to_path' => ['required', 'string', 'max:255'],
            'status_code' => ['required', 'integer', 'in:301,302'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $data['from_path'] = Redirect::normalizePath($data['from_path']);
        $data['is_active'] = $request->has('is_active');

        return $data;
    }
}
