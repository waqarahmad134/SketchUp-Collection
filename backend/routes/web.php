<?php

use App\Http\Controllers\BlogController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProductPageController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/blog', [BlogController::class, 'index'])->name('blog.index');
Route::get('/blog/{slug}', [BlogController::class, 'show'])->name('blog.show');

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
Route::get('/signup', [AuthController::class, 'showRegister'])->name('register');
Route::post('/signup', [AuthController::class, 'register'])->name('register.submit');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::get('/bundles', [ProductPageController::class, 'index'])->name('bundles.index');
Route::get('/bundles/{slug}', [ProductPageController::class, 'show'])->name('bundles.show');

Route::view('/about', 'pages.about', [
    'title' => 'About Us - 3DAssetHub',
    'metaDescription' => 'Learn about 3DAssetHub, our mission to empower designers, and our commitment to providing premium 3D assets.',
])->name('about');

Route::view('/pricing', 'pages.pricing', [
    'title' => 'Pricing - 3DAssetHub',
    'metaDescription' => 'Flexible pricing options for designers of all levels.',
])->name('pricing');

Route::view('/contact', 'pages.contact', [
    'title' => 'Contact Us - 3DAssetHub',
    'metaDescription' => 'Have questions? Contact our team.',
])->name('contact');

Route::view('/careers', 'pages.careers', [
    'title' => 'Careers - 3DAssetHub',
    'metaDescription' => 'Join the 3DAssetHub team.',
])->name('careers');

Route::view('/support', 'pages.support', [
    'title' => 'Support - 3DAssetHub',
    'metaDescription' => 'Get help with 3DAssetHub.',
])->name('support');

Route::view('/community', 'pages.community', [
    'title' => 'Community - 3DAssetHub',
    'metaDescription' => 'Join our designer community.',
])->name('community');

Route::view('/free-assets', 'pages.free-assets', [
    'title' => 'Free Assets - 3DAssetHub',
    'metaDescription' => 'Download free premium 3D assets.',
])->name('free-assets');

Route::view('/documentation', 'pages.documentation', [
    'title' => 'Documentation - 3DAssetHub',
    'metaDescription' => 'Guides and docs for using 3DAssetHub.',
])->name('documentation');

Route::view('/tutorials', 'pages.tutorials', [
    'title' => 'Tutorials - 3DAssetHub',
    'metaDescription' => 'Video guides and tutorials.',
])->name('tutorials');

Route::view('/updates', 'pages.updates', [
    'title' => 'Updates - 3DAssetHub',
    'metaDescription' => 'Latest news and releases.',
])->name('updates');

Route::view('/license', 'pages.license', [
    'title' => 'License Agreement - 3DAssetHub',
    'metaDescription' => 'Understand your licensing rights.',
])->name('license');

Route::view('/privacy-policy', 'pages.privacy-policy', [
    'title' => 'Privacy Policy - 3DAssetHub',
    'metaDescription' => 'How we collect, use, and protect your data.',
])->name('privacy-policy');

Route::view('/terms-of-service', 'pages.terms-of-service', [
    'title' => 'Terms of Service - 3DAssetHub',
    'metaDescription' => 'Rules and guidelines for using 3DAssetHub.',
])->name('terms-of-service');
