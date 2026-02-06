<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Mobile\Cashier\Auth\CashierMobileAuthController;
use App\Http\Controllers\Api\Mobile\Cashier\Order\CashierMobileOrderController;

Route::prefix('v1/mobile')->group(function () {

    Route::prefix('cashier')->group(function () {
        Route::get('/test', function () {
            return response()->json([
                'status' => 'ok',
                'message' => 'API route is working',
            ]);
        });
        Route::post('/login', [CashierMobileAuthController::class, 'login']);

        // ✅ DENGAN TOKEN
        Route::middleware('auth:employee_api')->group(function () {
            Route::get('/me', [CashierMobileAuthController::class, 'me']);
            Route::post('/logout', [CashierMobileAuthController::class, 'logout']);

            Route::get('/products', [CashierMobileOrderController::class, 'getProducts']);

        });
    });

});
