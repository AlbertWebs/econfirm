<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\DashboardController;
// MpesaController
use App\Http\Controllers\MpesaController;


Route::get('/', function () {
    return view('welcome');
});

Route::get('/portal', [HomeController::class, 'portal'])->name('portal');
// transaction.search
// Route::post('/transaction.search', [HomeController::class, 'searchTransactions'])->name('transaction.search');
//Transaction
Route::get('/get-transaction/{id}', [HomeController::class, 'transaction'])->name('transaction.index');

Route::get('/approve-transaction/{id}', [HomeController::class, 'approveTransaction'])->name('approve.transaction');
// portal
Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

Route::get('/view/{id}', [DashboardController::class, 'viewTransaction'])->name('view.transaction');
//approve transaction
// Route::get('/approve-transaction/{id}', [DashboardController::class, 'approveTransaction'])->name('approve.transaction');
//approve transaction
Route::post('/approve-transaction/{id}', [DashboardController::class, 'approveTransaction'])->name('transaction.approve');
//reject transaction
Route::post('/reject-transaction/{id}', [DashboardController::class, 'rejectTransaction'])->name('reject.transaction');
//transaction.export
Route::get('/transaction.export', [DashboardController::class, 'exportTransactions'])->name('transaction.export');

Route::post('/submit-transaction', [HomeController::class, 'submitTransaction'])->name('submit.transaction');
Route::get('/transaction/search', [HomeController::class, 'searchTransactions'])->name('transaction.search');

//get access token
Route::get('/get-access-token', [HomeController::class, 'getAccessToken'])->name('get.access.token');

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

// API routes
Route::prefix('api')->group(function () {
    Route::post('/mpesa/callback', [MpesaController::class, 'handleCallback'])->name('mpesa.callback');
    Route::post('/mpesa/transaction', [MpesaController::class, 'transaction'])->name('mpesa.transaction');
});
