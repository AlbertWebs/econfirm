<?php


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MpesaController;

// Example API route
Route::get('/ping', function () {
    return response()->json(['message' => 'API is working!']);
});
Route::post('/mpesa/callback', [MpesaController::class, 'handleCallback'])->name('mpesa.callback');