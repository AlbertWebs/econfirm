<?php

use App\Http\Controllers\Api\PaymentsApiController;
use App\Http\Controllers\EscrowApiController;
use App\Http\Controllers\MobileApiController;
use App\Http\Controllers\MpesaController;
use Illuminate\Support\Facades\Route;

// Example API route
Route::get('/ping', function () {
    return response()->json(['message' => 'API is working!']);
});
Route::post('/mpesa/callback', [MpesaController::class, 'handleCallback'])->name('mpesa.callback');
Route::post('/mpesa/b2b/callback', [MpesaController::class, 'handleB2BCallback'])->name('mpesa.b2b.callback');
Route::post('/mpesa/b2c/callback', [MpesaController::class, 'handleB2CCallback'])->name('mpesa.b2c.callback');
Route::post('/mpesa/b2c/timeout', [MpesaController::class, 'handleB2CCallback'])->name('mpesa.b2c.timeout');
Route::post('/mpesa/reversal/result', [MpesaController::class, 'handleReversalResult'])->name('mpesa.reversal.result');
Route::post('/mpesa/reversal/timeout', [MpesaController::class, 'handleReversalTimeout'])->name('mpesa.reversal.timeout');

// Authentication routes (public)
Route::post('/auth/send-otp', [MobileApiController::class, 'sendOtp']);
Route::post('/auth/verify-otp', [MobileApiController::class, 'verifyOtp']);

// Mobile API Routes
Route::prefix('mobile')->group(function () {
    // Public endpoints
    Route::get('/transaction-types', [MobileApiController::class, 'getTransactionTypes']);
    Route::post('/transaction/create', [MobileApiController::class, 'createTransaction']);
    Route::middleware('deprecated.mobile.payment')->group(function () {
        Route::post('/payment/initiate', [MobileApiController::class, 'initiatePayment']);
        Route::post('/payment/status', [MobileApiController::class, 'checkPaymentStatus']);
    });
    Route::get('/transaction/{transactionId}', [MobileApiController::class, 'getTransaction']);
    Route::post('/transaction/search', [MobileApiController::class, 'searchTransaction']);
});

// Escrow + payment gateway API (v1) — all M-Pesa traffic is initiated server-side only.
Route::prefix('v1')->middleware(['api.auth', 'throttle:1000,60'])->group(function () {
    Route::post('/transactions', [EscrowApiController::class, 'createTransaction']);
    Route::get('/transactions/{transaction_id}', [EscrowApiController::class, 'getTransaction']);
    Route::post('/transactions/{transaction_id}/release', [EscrowApiController::class, 'releaseFunds']);
    Route::post('/transactions/{transaction_id}/reversal', [PaymentsApiController::class, 'reversal'])
        ->middleware('throttle:60,1');
    Route::post('/payments/stk-push', [PaymentsApiController::class, 'stkPush'])->middleware('throttle:120,1');
    Route::post('/payments/c2b', [PaymentsApiController::class, 'c2b'])->middleware('throttle:60,1');
    Route::post('/payments/b2c', [PaymentsApiController::class, 'b2c'])->middleware('throttle:60,1');
    Route::post('/payments/b2b', [PaymentsApiController::class, 'b2b'])->middleware('throttle:60,1');
});
