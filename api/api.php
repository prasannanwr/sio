<?php

use App\Http\Controllers\Api\PaymentController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes for Monnify Payments (consumed by an Android app)
|--------------------------------------------------------------------------
*/

// Webhook is unauthenticated but signature-verified inside the controller.
// It also needs to be excluded from CSRF (in app/Http/Middleware/VerifyCsrfToken.php
// add 'api/payments/webhook' to the $except array, or rely on the default
// behavior of the api middleware group, which is stateless).
Route::post('payments/webhook', [PaymentController::class, 'webhook'])
    ->name('payments.webhook');

// Authenticated app endpoints. Use Sanctum (or whatever auth your app uses).
Route::middleware('auth:sanctum')->group(function () {
    Route::post('payments/initialize',                    [PaymentController::class, 'initialize'])->name('payments.initialize');
    Route::get('payments/verify/{paymentReference}',      [PaymentController::class, 'verify'])->name('payments.verify');
    Route::get('payments/{paymentReference}',             [PaymentController::class, 'show'])->name('payments.show');
    Route::get('payments',                                [PaymentController::class, 'index'])->name('payments.index');
});
