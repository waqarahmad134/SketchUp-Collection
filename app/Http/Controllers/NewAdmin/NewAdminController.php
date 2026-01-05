<?php

namespace App\Http\Controllers\NewAdmin;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Models\PostCategory;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NewAdminController extends Controller
{
    public function showLogin()
    {
        return view('new admin.admin.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            return redirect()->intended(route('newadmin.dashboard'));
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('newadmin.login');
    }

    public function dashboard()
    {
        $posts = Post::with('category')->latest()->get();
        $categories = PostCategory::active()->get();
        $tags = Tag::where('type', 'post')->active()->get();

        // Add view_count accessor for compatibility with views
        $posts->each(function ($post) {
            $post->view_count = $post->views;
        });

        return view('new admin.admin.dashboard', compact('posts', 'categories', 'tags'));
    }
}
