<?php

use App\Http\Controllers\BlogController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProductPageController;
use App\Http\Controllers\CreatorController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\CartController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/blog', [BlogController::class, 'index'])->name('blog.index');
Route::get('/blog/{slug}', [BlogController::class, 'show'])->name('blog.show');

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
Route::get('/signup', [AuthController::class, 'showRegister'])->name('register');
Route::post('/signup', [AuthController::class, 'register'])->name('register.submit');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Profile route
Route::middleware('auth')->group(function () {
    Route::get('/profile', function () {
        $userId = Auth::id();
        abort_unless($userId, 403);
        return redirect()->route('creators.show', $userId);
    })->name('profile');

    Route::post('/profile/payment-method', [CreatorController::class, 'updatePaymentMethod'])
        ->name('profile.payment-method.update');

    Route::get('/checkout', [CheckoutController::class, 'show'])->name('checkout.show');
    Route::post('/checkout', [CheckoutController::class, 'process'])->name('checkout.process');
    Route::get('/checkout/stripe/start', [CheckoutController::class, 'stripeStart'])->name('checkout.stripe.start');
    Route::get('/checkout/success', [CheckoutController::class, 'success'])->name('checkout.success');
    Route::get('/checkout/cancel', [CheckoutController::class, 'cancel'])->name('checkout.cancel');
});

Route::get('/bundles', [ProductPageController::class, 'index'])->name('bundles.index');
Route::get('/bundles/{slug}', [ProductPageController::class, 'show'])->name('bundles.show');
Route::get('/bundles/{slug}/download', [ProductPageController::class, 'download'])->name('bundles.download');
Route::post('/bundles/{slug}/reviews', [ReviewController::class, 'store'])->name('reviews.store');
Route::post('/bundles/{product}/add-to-cart', [CartController::class, 'add'])->name('cart.add');
Route::post('/bundles/{product}/buy-now', [CartController::class, 'buyNow'])->name('cart.buyNow');
Route::get('/cart', [CartController::class, 'show'])->name('cart.show');

Route::get('/creators/{user}', [CreatorController::class, 'show'])->name('creators.show');

Route::view('/about', 'pages.about', [
    'title' => 'About Us - SketchUp Collection',
    'metaDescription' => 'Learn about SketchUp Collection, our mission to empower designers, and our commitment to providing premium 3D assets.',
])->name('about');

Route::view('/pricing', 'pages.pricing', [
    'title' => 'Pricing - SketchUp Collection',
    'metaDescription' => 'Flexible pricing options for designers of all levels.',
])->name('pricing');

Route::view('/contact', 'pages.contact', [
    'title' => 'Contact Us - SketchUp Collection',
    'metaDescription' => 'Have questions? Contact our team.',
])->name('contact');

Route::view('/careers', 'pages.careers', [
    'title' => 'Careers - SketchUp Collection',
    'metaDescription' => 'Join the SketchUp Collection team.',
])->name('careers');

Route::view('/support', 'pages.support', [
    'title' => 'Support - SketchUp Collection',
    'metaDescription' => 'Get help with SketchUp Collection.',
])->name('support');

Route::view('/community', 'pages.community', [
    'title' => 'Community - SketchUp Collection',
    'metaDescription' => 'Join our designer community.',
])->name('community');

Route::view('/free-assets', 'pages.free-assets', [
    'title' => 'Free Assets - SketchUp Collection',
    'metaDescription' => 'Download free premium 3D assets.',
])->name('free-assets');

Route::view('/documentation', 'pages.documentation', [
    'title' => 'Documentation - SketchUp Collection',
    'metaDescription' => 'Guides and docs for using SketchUp Collection.',
])->name('documentation');

Route::view('/tutorials', 'pages.tutorials', [
    'title' => 'Tutorials - SketchUp Collection',
    'metaDescription' => 'Video guides and tutorials.',
])->name('tutorials');

Route::view('/updates', 'pages.updates', [
    'title' => 'Updates - SketchUp Collection',
    'metaDescription' => 'Latest news and releases.',
])->name('updates');

Route::view('/license', 'pages.license', [
    'title' => 'License Agreement - SketchUp Collection',
    'metaDescription' => 'Understand your licensing rights.',
])->name('license');

Route::view('/privacy-policy', 'pages.privacy-policy', [
    'title' => 'Privacy Policy - SketchUp Collection',
    'metaDescription' => 'How we collect, use, and protect your data.',
])->name('privacy-policy');

Route::view('/terms-of-service', 'pages.terms-of-service', [
    'title' => 'Terms of Service - SketchUp Collection',
    'metaDescription' => 'Rules and guidelines for using SketchUp Collection.',
])->name('terms-of-service');
