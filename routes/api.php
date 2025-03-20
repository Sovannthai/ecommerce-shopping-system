<?php

use App\Helpers\Routes\RouteHelper;
use Illuminate\Support\Facades\Route;

// API Products Routes
Route::apiResource('products', App\Http\Controllers\Api\ProductController::class);

// API Reviews Routes
Route::apiResource('reviews', App\Http\Controllers\Api\ReviewController::class);
Route::get('products/{product}/reviews', [App\Http\Controllers\Api\ReviewController::class, 'getProductReviews']);

// API Categories Routes
Route::apiResource('categories', App\Http\Controllers\Api\CategoryController::class);
Route::get('categories/tree', [App\Http\Controllers\Api\CategoryController::class, 'tree']);

// API Brands Routes
Route::apiResource('brands', App\Http\Controllers\Api\BrandController::class);

// API Cart Routes
Route::prefix('cart')->group(function () {
    Route::post('/get', [App\Http\Controllers\Api\CartController::class, 'getCart']);
    Route::post('/add', [App\Http\Controllers\Api\CartController::class, 'addItem']);
    Route::post('/update/{itemId}', [App\Http\Controllers\Api\CartController::class, 'updateItem']);
    Route::delete('/remove/{itemId}', [App\Http\Controllers\Api\CartController::class, 'removeItem']);
    Route::post('/clear', [App\Http\Controllers\Api\CartController::class, 'clearCart']);
    Route::post('/transfer', [App\Http\Controllers\Api\CartController::class, 'transferCart']);
});

// API Order Routes
Route::apiResource('orders', App\Http\Controllers\Api\OrderController::class)->except(['update', 'destroy']);
Route::post('orders/{id}/cancel', [App\Http\Controllers\Api\OrderController::class, 'cancel']);
Route::post('orders/{id}/status', [App\Http\Controllers\Api\OrderController::class, 'updateStatus']);
Route::get('orders/statistics', [App\Http\Controllers\Api\OrderController::class, 'statistics']);

