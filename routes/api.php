<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Broadcast;
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
            Route::post('/broadcasting/auth', function (Request $request) {
                // Pastikan user resolver pakai guard employee_api
                $request->setUserResolver(fn () => auth('employee_api')->user());

                return Broadcast::auth($request);
            });

            Route::get('/products', [CashierMobileOrderController::class, 'getProducts']);
            Route::get('/get-orders-data/{tab}', [CashierMobileOrderController::class, 'getOrdersData']);
            Route::post('/checkout', [CashierMobileOrderController::class, 'checkout']);
            Route::get('/order-detail/{id}', [CashierMobileOrderController::class, 'orderDetail']);
            Route::get('/print-detail/{id}', [CashierMobileOrderController::class, 'printDetail']);
            Route::post('/delete-order/{id}', [CashierMobileOrderController::class, 'softDeleteUnpaidOrder']);
            Route::post('/payment-order/{id}', [CashierMobileOrderController::class, 'paymentOrder']);
            Route::post('/process-order/{id}', [CashierMobileOrderController::class, 'processOrder']);
            Route::post('/finish-order/{id}', [CashierMobileOrderController::class, 'finishOrder']);
            Route::post('/cancel-process-order/{id}', [CashierMobileOrderController::class, 'cancelProcessOrder']);
        });
    });
});
