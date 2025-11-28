<?php


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MpesaController;
use App\Http\Controllers\MobileApiController;

// Example API route
Route::get('/ping', function () {
    return response()->json(['message' => 'API is working!']);
});
Route::post('/mpesa/callback', [MpesaController::class, 'handleCallback'])->name('mpesa.callback');
Route::post('/mpesa/b2b/callback', [MpesaController::class, 'handleB2BCallback'])->name('mpesa.b2b.callback');
Route::post('/mpesa/b2c/callback', [MpesaController::class, 'handleB2CCallback'])->name('mpesa.b2c.callback');

// Authentication routes (public)
Route::post('/auth/send-otp', [MobileApiController::class, 'sendOtp']);
Route::post('/auth/verify-otp', [MobileApiController::class, 'verifyOtp']);

// Mobile API Routes
Route::prefix('mobile')->group(function () {
    // Public endpoints
    Route::get('/transaction-types', [MobileApiController::class, 'getTransactionTypes']);
    Route::post('/transaction/create', [MobileApiController::class, 'createTransaction']);
    Route::post('/payment/initiate', [MobileApiController::class, 'initiatePayment']);
    Route::post('/payment/status', [MobileApiController::class, 'checkPaymentStatus']);
    Route::get('/transaction/{transactionId}', [MobileApiController::class, 'getTransaction']);
    Route::post('/transaction/search', [MobileApiController::class, 'searchTransaction']);
});