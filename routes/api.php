<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;



Route::get('/test', [MpesaController::class, 'index']);
Route::post('/mpesa/callback', [MpesaController::class, 'handleCallback']);