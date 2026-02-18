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
            Route::get('/get-orders-data/{tab}', [CashierMobileOrderController::class, 'getOrdersData']);
            Route::post('/checkout', [CashierMobileOrderController::class, 'checkout']);
            Route::get('/order-detail/{id}', [CashierMobileOrderController::class, 'orderDetail']);
            Route::post('/delete-order/{id}', [CashierMobileOrderController::class, 'softDeleteUnpaidOrder']);
            Route::post('/payment-order/{id}', [CashierMobileOrderController::class, 'paymentOrder']);
        });
    });

});
