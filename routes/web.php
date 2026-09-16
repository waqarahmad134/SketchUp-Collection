<?php

use App\Http\Controllers\BlogController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProductPageController;
use App\Http\Controllers\CreatorController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\DailyLoginController;
use App\Http\Controllers\SitemapController;
use App\Http\Controllers\RobotsController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\AdminPostController;
use App\Http\Controllers\Admin\AdminCategoryController;
use App\Http\Controllers\Admin\AdminTagController;
use App\Http\Controllers\Admin\AdminMediaController;
use App\Http\Controllers\Admin\AdminProductController;
use App\Http\Controllers\Admin\AdminProductCategoryController;
use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\Admin\AdminOrderController;
use App\Http\Controllers\Admin\AdminTransactionController;
use App\Http\Controllers\Admin\AdminCouponController;
use App\Http\Controllers\Admin\AdminMenuController;
use App\Http\Controllers\Admin\AdminSettingController;
use App\Http\Controllers\Admin\AdminCustomScriptController;
use App\Http\Controllers\Admin\AdminPaymentGatewayController;
use App\Http\Controllers\PaymentController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

// SEO Routes
Route::get('/sitemap.xml', [SitemapController::class, 'index'])->name('sitemap');
Route::get('/robots.txt', [RobotsController::class, 'index'])->name('robots');
Route::get('/llms.txt', [LlmsController::class, 'index'])->name('llms');

// NOTE: Debug/maintenance routes (/clear, /migrations, /seed, /storage-link)
// were removed. They ran Artisan commands over plain GET requests with no
// authentication, so anyone could wipe the production database.


Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/blog', [BlogController::class, 'index'])->name('blog.index');
Route::get('/blog/{slug}', [BlogController::class, 'show'])->name('blog.show');

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
Route::get('/signup', [AuthController::class, 'showRegister'])->name('register');
Route::post('/signup', [AuthController::class, 'register'])->name('register.submit');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Profile route
// Daily Login Bonus - status available to everyone, claim requires auth
Route::get('/daily-login/status', [DailyLoginController::class, 'status'])->name('daily-login.status');

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

    // Configurable payment gateways (JazzCash, Easypaisa, PayPro, Paddle, Lemon Squeezy, Polar)
    Route::post('/checkout/pay/{gateway}', [PaymentController::class, 'start'])->name('payment.start')->middleware('auth');
    Route::match(['get', 'post'], '/payment/callback/{gateway}', [PaymentController::class, 'callback'])->name('payment.callback');
    Route::post('/payment/webhook/{gateway}', [PaymentController::class, 'webhook'])->name('payment.webhook');
    
    // Daily Login Bonus - claim requires authentication
    Route::post('/daily-login/claim', [DailyLoginController::class, 'claim'])->name('daily-login.claim');
});

Route::get('/bundles', [ProductPageController::class, 'index'])->name('bundles.index');
Route::get('/bundles/{slug}', [ProductPageController::class, 'show'])->name('bundles.show');
Route::get('/bundles/{slug}/download', [ProductPageController::class, 'download'])->name('bundles.download');
Route::post('/bundles/{slug}/reviews', [ReviewController::class, 'store'])->name('reviews.store');
Route::post('/bundles/{product}/add-to-cart', [CartController::class, 'add'])->name('cart.add');
Route::post('/bundles/{product}/buy-now', [CartController::class, 'buyNow'])->name('cart.buyNow');
Route::get('/cart', [CartController::class, 'show'])->name('cart.show');
Route::post('/cart/coupon/apply', [CartController::class, 'applyCoupon'])->name('cart.coupon.apply');
Route::post('/cart/coupon/remove', [CartController::class, 'removeCoupon'])->name('cart.coupon.remove');
Route::post('/cart/coins/apply', [CartController::class, 'applyCoins'])->name('cart.coins.apply');
Route::post('/cart/coins/remove', [CartController::class, 'removeCoins'])->name('cart.coins.remove');
Route::delete('/cart/item/{productId}', [CartController::class, 'remove'])->name('cart.remove');
Route::post('/cart/clear', [CartController::class, 'clear'])->name('cart.clear');

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

// Admin Panel Routes (Development)
Route::prefix('admin')->name('admin.')->group(function () {
    // Login routes (guest middleware - only accessible when not logged in)
    Route::middleware('guest')->group(function () {
        Route::get('/login', [AdminController::class, 'showLogin'])->name('login');
        Route::post('/login', [AdminController::class, 'login']);
    });

    // Protected admin routes (auth middleware)
    Route::middleware('auth')->group(function () {
        Route::post('/logout', [AdminController::class, 'logout'])->name('logout');
        Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
        
        // Posts routes
        Route::resource('posts', AdminPostController::class);
        
        // Categories routes
        Route::get('/categories', [AdminCategoryController::class, 'index'])->name('categories.index');
        Route::post('/categories', [AdminCategoryController::class, 'store'])->name('categories.store');
        Route::put('/categories/{id}', [AdminCategoryController::class, 'update'])->name('categories.update');
        Route::delete('/categories/{id}', [AdminCategoryController::class, 'destroy'])->name('categories.destroy');
        
        // Tags routes
        Route::get('/tags', [AdminTagController::class, 'index'])->name('tags.index');
        Route::post('/tags', [AdminTagController::class, 'store'])->name('tags.store');
        Route::put('/tags/{id}', [AdminTagController::class, 'update'])->name('tags.update');
        Route::delete('/tags/{id}', [AdminTagController::class, 'destroy'])->name('tags.destroy');
        
        // Media routes
        Route::get('/media', [AdminMediaController::class, 'index'])->name('media.index');
        Route::post('/media', [AdminMediaController::class, 'store'])->name('media.store');
        Route::delete('/media/{id}', [AdminMediaController::class, 'destroy'])->name('media.destroy');
        
        // Products routes
        Route::resource('products', AdminProductController::class);
        
        // Product Categories routes
        Route::resource('product-categories', AdminProductCategoryController::class);
        
        // Users routes
        Route::resource('users', AdminUserController::class);
        
        // Orders routes
        Route::resource('orders', AdminOrderController::class);
        
        // Transactions routes
        Route::resource('transactions', AdminTransactionController::class);
        
        // Coupons routes
        Route::resource('coupons', AdminCouponController::class);
        
        // Menus routes
        Route::resource('menus', AdminMenuController::class);
        
        // Settings routes
        Route::get('/settings', [AdminSettingController::class, 'index'])->name('settings.index');
        Route::get('/settings/{key}/edit', [AdminSettingController::class, 'edit'])->name('settings.edit');
        Route::put('/settings/{key}', [AdminSettingController::class, 'update'])->name('settings.update');
        Route::post('/settings', [AdminSettingController::class, 'store'])->name('settings.store');
        
        // Custom Scripts routes
        Route::resource('custom-scripts', AdminCustomScriptController::class);

        // Payment Gateways routes
        Route::get('/payment-gateways', [AdminPaymentGatewayController::class, 'index'])->name('payment-gateways.index');
        Route::get('/payment-gateways/{gateway}/edit', [AdminPaymentGatewayController::class, 'edit'])->name('payment-gateways.edit');
        Route::put('/payment-gateways/{gateway}', [AdminPaymentGatewayController::class, 'update'])->name('payment-gateways.update');
        Route::post('/payment-gateways/{gateway}/toggle', [AdminPaymentGatewayController::class, 'toggle'])->name('payment-gateways.toggle');
        Route::post('/payment-gateways-rate', [AdminPaymentGatewayController::class, 'updateRate'])->name('payment-gateways.rate');
    });
});
