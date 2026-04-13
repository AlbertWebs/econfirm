<?php

use Illuminate\Support\Facades\Route;
use App\Models\ScamReport;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ContractController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MpesaController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\Auth\PhoneOtpLoginController;



// HomeController routes
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/v2', [HomeController::class, 'indexV2'])->name('home.v2');
Route::get('/portal', [HomeController::class, 'portal'])->name('portal');
Route::get('/features', [HomeController::class, 'features'])->name('features');
Route::get('/get-transaction/{id}', [HomeController::class, 'transaction'])->name('transaction.index');
Route::get('/approve-transaction/{id}', [HomeController::class, 'approveTransaction'])->name('approve.transaction');
Route::post('/submit-transaction', [HomeController::class, 'submitTransaction'])->name('submit.transaction');
Route::get('/transaction/search', [HomeController::class, 'searchTransactions'])->name('transaction.search');
Route::get('/terms-and-conditions', [HomeController::class, 'termsAndConditions'])->name('terms.conditions');
Route::get('/privacy-policy', [HomeController::class, 'privacyPolicy'])->name('privacy.policy');
Route::get('/complience', [HomeController::class, 'complience'])->name('complience');
Route::get('/security', [HomeController::class, 'security'])->name('security');
Route::get('/support', [HomeController::class, 'support'])->name('support');
Route::get('/help', [HomeController::class, 'help'])->name('help');
Route::get('/contact', [HomeController::class, 'contact'])->name('contact');
Route::post('/contact', [HomeController::class, 'submitContact'])->name('submit.contact');
Route::get('/sitemap.xml', [HomeController::class, 'sitemap'])->name('sitemap');
Route::get('/scam-watch', [HomeController::class, 'scamWatch'])->name('scam.watch');
Route::get('/scam-watch/report', [HomeController::class, 'scamWatchReportForm'])->name('scam.watch.report');
Route::get('/scam-watch/category/{category}', [HomeController::class, 'scamWatchCategory'])
    ->name('scam.watch.category')
    ->whereIn('category', array_keys(ScamReport::CATEGORY_LABELS));
Route::get('/scam-watch/reports/{report}/{slug?}', [HomeController::class, 'scamWatchShow'])->name('scam.watch.show');
Route::post('/submit-scam-report', [HomeController::class, 'submitScamReport'])->name('submit.scam.report');
Route::post('/like-scam-report/{id}', [HomeController::class, 'likeScamReport'])->name('like.scam.report');
Route::get('/transaction/status/{id}', [HomeController::class, 'transactionStatus'])->name('transaction.status');
Route::get('/get-access-token', [HomeController::class, 'getAccessToken'])->name('get.access.token');
Route::post('/create-otp', [HomeController::class, 'createOTP'])->name('create.otp');
Route::get('/api/documentation', [HomeController::class, 'getAPIDocumentation'])->name('api-documentation');

Route::get('/e-contract', [HomeController::class, 'getEContract'])->name('e-contract');
Route::get('/e-contract-print/{transactionID}', [ContractController::class, 'generateEscrowPdf'])->name('e-contract.print');

// Test SMS route - Send SMS to +254723014032
Route::get('/test-sms', [HomeController::class, 'testSms'])->name('test.sms');
Route::get('/test-sms/{phone}', [HomeController::class, 'testSms'])->name('test.sms.phone'); 



Route::post('/approve-transaction-post/{id}', [HomeController::class, 'approveTransactionPost'])->name('transaction.approve');
Route::post('/custom-login', [DashboardController::class, 'customLogin'])->name('custom.login');



Auth::routes();

Route::middleware('guest')->group(function () {
    Route::post('/login/phone/send-otp', [PhoneOtpLoginController::class, 'sendOtp'])
        ->middleware('throttle:5,1')
        ->name('login.phone.send-otp');
    Route::post('/login/phone', [PhoneOtpLoginController::class, 'verify'])
        ->middleware('throttle:12,1')
        ->name('login.phone');
    Route::get('/login/phone/cancel', [PhoneOtpLoginController::class, 'cancel'])
        ->name('login.phone.cancel');
});

// DashboardController routes

Route::get('/home', [DashboardController::class, 'index'])->name('home.dashboard');
Route::get('/view/{id}', [DashboardController::class, 'viewTransaction'])->name('view.transaction');
Route::post('/approve-transaction/{id}', [DashboardController::class, 'approveTransaction'])->name('transaction.approves');
Route::post('/reject-transaction/{id}', [DashboardController::class, 'rejectTransaction'])->name('reject.transaction');
Route::get('/transaction.export', [DashboardController::class, 'exportTransactions'])->name('transaction.export');
Route::post('/user/update', [DashboardController::class, 'update'])->name('user.update');
Route::post('/user/update-password', [DashboardController::class, 'updatePassword'])->name('user.update-password');

Route::get('/profile/edit/{id}', [DashboardController::class, 'editProfile'])->name('profile.edit');
Route::post('/profile/update', [DashboardController::class, 'updateProfile'])->name('profile.update');

// Admin routes
/*------------------------------------------
--------------------------------------------
All Normal Users Routes List
--------------------------------------------
--------------------------------------------*/
Route::middleware(['auth', 'user-access:user'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('user.dashboard');    
});
/*------------------------------------------
--------------------------------------------
All Admin Routes List
--------------------------------------------
--------------------------------------------*/
Route::middleware(['auth', 'user-access:admin'])->group(function () {
    Route::get('/admin/dashboard', [AdminController::class, 'index'])->name('admin.dashboard');
});
