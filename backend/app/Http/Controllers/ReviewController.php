<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Review;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function store(Request $request, string $slug): RedirectResponse
    {
        $product = Product::where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'comment' => ['nullable', 'string', 'max:2000'],
        ]);

        Review::create([
            'product_id' => $product->id,
            'user_id' => $request->user()?->id,
            'name' => $data['name'],
            'email' => $data['email'] ?? null,
            'rating' => $data['rating'],
            'comment' => $data['comment'] ?? null,
            'status' => 'approved', // simple flow; could be pending + moderation
        ]);

        return back()->with('status', 'Thank you! Your review has been submitted.');
    }
}

