<?php

use App\Http\Middleware\SetLocale;
use Illuminate\Support\Facades\Auth;
use App\Http\Middleware\Localization;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\IDCardController;
use App\Http\Controllers\SliderController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\FileUploadController;
use App\Http\Controllers\Auth\TelegramController;
use App\Http\Controllers\Backends\RoleController;
use App\Http\Controllers\Backends\UserController;
use App\Http\Controllers\Backends\GoogleController;
use App\Http\Controllers\Backends\CategoryController;
use App\Http\Controllers\Backends\CustomerController;
use App\Http\Controllers\Backends\PermissionController;
use App\Http\Controllers\Backends\ReviewController;
use App\Http\Controllers\Backends\OrderController;

Route::get('/', function () {
    return view('auth.login');
});
Route::get('language/{locale}', function ($locale) {
    app()->setLocale($locale);
    session()->put('locale', $locale);
    $language = \App\Models\BusinessSetting::first()->language;
    session()->put('language_settings', $language);
    return redirect()->back();
})->name('change_language');
//Login with telegram
Route::get('login/telegram', [LoginController::class, 'redirectToTelegram'])->name('login.telegram');
Route::post('login/telegram/callback', [LoginController::class, 'handleTelegramCallback'])->name('telegram.callback');
Route::post('/Shopping-Backend/telegram/webhook', [TelegramController::class, 'webhook']);
// routes/web.php
Route::post('/api/telegram-login', [TelegramController::class, 'telegramLogin'])->name('store_user.telegram');
Route::get('/telegram_callback', [TelegramController::class, 'telegramAuthCallback'])->name('telegram_callback');
//Google Login
Route::controller(GoogleController::class)->group(function () {
    Route::get('auth/google', 'redirectToGoogle')->name('auth.google');
    Route::get('auth/google/callback', 'handleGoogleCallback');
});

Route::middleware(['auth', Localization::class, SetLocale::class,])->group(function () {
    Route::resource('roles', RoleController::class);
    Route::resource('permission', PermissionController::class);
    Route::resource('users', UserController::class);
    Route::get('/user-profile/{id}', [UserController::class, 'view_profile'])->name('user.view_profile');
    Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
    Route::resource('categories', CategoryController::class);
    Route::get('/categories-tree', [CategoryController::class, 'tree'])->name('categories.tree');
    Route::resource('sliders', SliderController::class);
    Route::resource('customers', CustomerController::class);
    Route::resource('reviews', ReviewController::class);
    Route::post('/reviews/{review}/approve', [ReviewController::class, 'approve'])->name('reviews.approve');
    Route::post('/reviews/{review}/reject', [ReviewController::class, 'reject'])->name('reviews.reject');
    Route::resource('brands', App\Http\Controllers\Backends\BrandController::class);
    Route::resource('products', App\Http\Controllers\Backends\ProductController::class);
    Route::post('/products/{productId}/images/{imageId}/primary', [App\Http\Controllers\Backends\ProductController::class, 'setPrimaryImage'])->name('products.setPrimaryImage');
    Route::delete('/product-images/{imageId}', [App\Http\Controllers\Backends\ProductController::class, 'deleteImage'])->name('products.deleteImage');

    // Order routes
    Route::get('/orders/dashboard', [OrderController::class, 'dashboard'])->name('orders.dashboard');
    Route::resource('orders', OrderController::class)->only(['index', 'show']);
    Route::post('/orders/{order}/update-status', [OrderController::class, 'updateStatus'])->name('orders.updateStatus');
    Route::post('/orders/{order}/cancel', [OrderController::class, 'cancel'])->name('orders.cancel');

    Route::get('/chunks_upload', [FileUploadController::class, 'index'])->name('chunks_upload.index');

    // Route for handling the chunked file upload
    Route::post('/chunks_upload', [FileUploadController::class, 'upload'])->name('chunks_upload.store');
    Route::get('/id-card/upload', [IDCardController::class, 'showUploadForm'])->name('id-card.upload.form');
    Route::post('/id-card/upload', [IDCardController::class, 'uploadAndDetect'])->name('id-card.upload');
});
Auth::routes();
