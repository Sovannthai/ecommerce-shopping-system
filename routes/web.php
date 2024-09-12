<?php

use App\Http\Middleware\SetLocale;
use Illuminate\Support\Facades\Auth;
use App\Http\Middleware\Localization;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SliderController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\FileUploadController;
use App\Http\Controllers\Auth\TelegramController;
use App\Http\Controllers\Backends\RoleController;
use App\Http\Controllers\Backends\UserController;
use App\Http\Controllers\Backends\GoogleController;
use App\Http\Controllers\Backends\CustomerController;
use App\Http\Controllers\Backends\PermissionController;

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
Route::get('login/telegram', [LoginController::class,'redirectToTelegram'])->name('login.telegram');
Route::post('login/telegram/callback',[LoginController::class,'handleTelegramCallback'])->name('telegram.callback');
Route::post('/Shopping-Backend/telegram/webhook', [TelegramController::class, 'webhook']);
// routes/web.php
Route::post('/api/telegram-login', [TelegramController::class, 'telegramLogin'])->name('store_user.telegram');
Route::get('/telegram_callback', [TelegramController::class, 'telegramAuthCallback'])->name('telegram_callback');
//Google Login
Route::controller(GoogleController::class)->group(function(){
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
    Route::resource('sliders', SliderController::class);
    Route::resource('customers', CustomerController::class);
    Route::get('/chunks_upload', [FileUploadController::class, 'index'])->name('chunks_upload.index');

    // Route for handling the chunked file upload
    Route::post('/chunks_upload', [FileUploadController::class, 'upload'])->name('chunks_upload.store');


});
Auth::routes();
