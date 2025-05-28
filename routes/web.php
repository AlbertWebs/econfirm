<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\DashboardController;

Route::get('/', function () {
    return view('welcome');
});
Route::get('/portal', function () {
    return view('portal');
});
// transaction.search
Route::post('/transaction.search', [HomeController::class, 'searchTransactions'])->name('transaction.search');
// portal
Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

Route::get('/view/{id}', [DashboardController::class, 'viewTransaction'])->name('view.transaction');
//approve transaction
Route::get('/approve-transaction/{id}', [DashboardController::class, 'approveTransaction'])->name('approve.transaction');
//approve transaction
Route::post('/approve-transaction/{id}', [DashboardController::class, 'approveTransaction'])->name('transaction.approve');
//reject transaction
Route::post('/reject-transaction/{id}', [DashboardController::class, 'rejectTransaction'])->name('reject.transaction');
//transaction.export
Route::get('/transaction.export', [DashboardController::class, 'exportTransactions'])->name('transaction.export');

Route::post('/submit-transaction', [HomeController::class, 'submitTransaction'])->name('submit.transaction');

//get access token
Route::get('/get-access-token', [HomeController::class, 'getAccessToken'])->name('get.access.token');

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
