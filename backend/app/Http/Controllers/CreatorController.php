<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\User;
use Illuminate\View\View;

class CreatorController extends Controller
{
    public function show(User $user): View
    {
        $products = Product::where('user_id', $user->id)
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        $avatar = $user->avatar ?? 'https://ui-avatars.com/api/?name=' . urlencode($user->name) . '&background=22b5ff&color=fff';

        return view('creators.show', [
            'title' => $user->name . ' - Creator Profile',
            'metaDescription' => 'View products uploaded by ' . $user->name,
            'user' => $user,
            'avatar' => $avatar,
            'products' => $products,
        ]);
    }
}

