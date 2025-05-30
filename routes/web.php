<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MpesaController;

// HomeController routes
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/portal', [HomeController::class, 'portal'])->name('portal');
Route::get('/get-transaction/{id}', [HomeController::class, 'transaction'])->name('transaction.index');
Route::get('/approve-transaction/{id}', [HomeController::class, 'approveTransaction'])->name('approve.transaction');
Route::post('/submit-transaction', [HomeController::class, 'submitTransaction'])->name('submit.transaction');
Route::get('/transaction/search', [HomeController::class, 'searchTransactions'])->name('transaction.search');
Route::get('/terms-and-conditions', [HomeController::class, 'termsAndConditions'])->name('terms.conditions');
Route::get('/privacy-policy', [HomeController::class, 'privacyPolicy'])->name('privacy.policy');
Route::get('/complience', [HomeController::class, 'complience'])->name('complience');
Route::get('/security', [HomeController::class, 'security'])->name('security');
Route::get('/transaction/status/{id}', [HomeController::class, 'transactionStatus'])->name('transaction.status');
Route::get('/get-access-token', [HomeController::class, 'getAccessToken'])->name('get.access.token');

// DashboardController routes
Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
Route::get('/view/{id}', [DashboardController::class, 'viewTransaction'])->name('view.transaction');
Route::post('/approve-transaction/{id}', [DashboardController::class, 'approveTransaction'])->name('transaction.approve');
Route::post('/reject-transaction/{id}', [DashboardController::class, 'rejectTransaction'])->name('reject.transaction');
Route::get('/transaction.export', [DashboardController::class, 'exportTransactions'])->name('transaction.export');


// Auth routes
Auth::routes();
// Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');


