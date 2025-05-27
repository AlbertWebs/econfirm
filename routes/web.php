<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;

Route::get('/', function () {
    return view('welcome');
});

Route::post('/submit-transaction', [HomeController::class, 'submitTransaction'])->name('submit.transaction');

//get access token
Route::get('/get-access-token', [HomeController::class, 'getAccessToken'])->name('get.access.token');
