<?php

use App\Helpers\Routes\RouteHelper;
use App\Http\Controllers\Api\ApiController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')
    ->group(function () {
        RouteHelper::includeRouteFiles(__DIR__ . '/api/v1');
});
Route::post('/customer/login', [ApiController::class, 'login']);
Route::post('/customer/register', [ApiController::class, 'register']);
Route::get('/get-all-customers', [ApiController::class, 'getAllCustomers']);
Route::get('/get-customer-detail', [ApiController::class, 'getCustomerDetail']);