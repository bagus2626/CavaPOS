<?php

use App\Http\Controllers\PaymentGateway\Xendit\WebhookController;
use Illuminate\Support\Facades\Route;

Route::prefix('xendit')->name('xendit.')->group(function () {
    Route::prefix('webhook')->group(function () {
        Route::post('invoice', [WebhookController::class, 'invoice']);
        Route::post('split-payment-status', [WebhookController::class, 'splitPaymentStatus']);
        Route::post('payout', [WebhookController::class, 'payout']);
        Route::post('sub-account/created', [WebhookController::class, 'subAccountCreated']);
    });
});
