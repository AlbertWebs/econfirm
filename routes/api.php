<?php


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MpesaController;

// Example API route
Route::get('/ping', function () {
    return response()->json(['message' => 'API is working!']);
});
Route::post('/mpesa/callback', [MpesaController::class, 'handleCallback'])->name('mpesa.callback');
Route::post('/mpesa/b2b/callback', [MpesaController::class, 'handleB2BCallback'])->name('mpesa.b2b.callback');
Route::post('/mpesa/b2c/callback', [MpesaController::class, 'handleB2CCallback'])->name('mpesa.b2c.callback');