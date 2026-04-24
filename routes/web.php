<?php

use App\Http\Controllers\Admin\ActivityLogController;
use App\Http\Controllers\Admin\BusinessController;
use App\Http\Controllers\Admin\ContactSubmissionController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\DisputeController as AdminDisputeController;
use App\Http\Controllers\Admin\LegalPageController;
use App\Http\Controllers\Admin\LiveChatController as AdminLiveChatController;
use App\Http\Controllers\Admin\MpesaTransactionsController;
use App\Http\Controllers\Admin\PageController as AdminPageController;
use App\Http\Controllers\Admin\ApiAccessController;
use App\Http\Controllers\Admin\ScamReportController as AdminScamReportController;
use App\Http\Controllers\Admin\SmsLogController;
use App\Http\Controllers\Admin\SiteSettingsController;
use App\Http\Controllers\Admin\SupportHelpItemController;
use App\Http\Controllers\Admin\TransactionController as AdminTransactionController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\AdminAuthController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\PhoneOtpLoginController;
use App\Http\Controllers\Auth\PhoneOtpRegisterController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\ContractController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DeveloperController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\LiveChatController;
use App\Models\ScamReport;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

// HomeController routes
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/v2', [HomeController::class, 'indexV2'])->name('home.v2');
Route::get('/portal', [HomeController::class, 'portal'])->name('portal');
Route::post('/portal/phone/send-otp', [HomeController::class, 'portalSendOtp'])->name('portal.phone.send-otp');
Route::post('/portal/phone/verify-otp', [HomeController::class, 'portalVerifyOtp'])->name('portal.phone.verify-otp');
Route::get('/features', [HomeController::class, 'features'])->name('features');
Route::get('/escrow/{product}', [HomeController::class, 'productEscrow'])->name('escrow.product')->whereIn('product', [
    'real-estate',
    'vehicle',
    'business',
    'ecommerce',
    'services',
    'freelancer',
    'rental',
    'import-export',
    'digital-asset',
    'construction',
    'equipment-machinery',
    'tender-contract',
    'education-school-fees',
    'marketplace',
]);
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
Route::post('/contact', [HomeController::class, 'submitContact'])
    ->middleware('throttle:8,1')
    ->name('submit.contact');
Route::get('/sitemap.xml', [HomeController::class, 'sitemap'])->name('sitemap');
Route::get('/scam-watch', [HomeController::class, 'scamWatch'])->name('scam.watch');
Route::get('/scam-watch/report', [HomeController::class, 'scamWatchReportForm'])->name('scam.watch.report');
Route::get('/scam-watch/category/{category}', [HomeController::class, 'scamWatchCategory'])
    ->name('scam.watch.category')
    ->whereIn('category', array_keys(ScamReport::CATEGORY_LABELS));
Route::get('/scam-watch/reports/{report}/evidence/{index}', [HomeController::class, 'scamWatchEvidence'])
    ->name('scam.watch.evidence')
    ->whereNumber('index');
Route::get('/scam-watch/reports/{report}/{slug?}', [HomeController::class, 'scamWatchShow'])->name('scam.watch.show');
Route::post('/scam-watch/reports/{report}/comments', [HomeController::class, 'postScamReportComment'])
    ->middleware('throttle:8,1')
    ->name('scam.watch.comments.store');
Route::get('/scam-watch/terms-of-use', [HomeController::class, 'scamWatchTermsOfUse'])->name('scam.watch.terms');
Route::post('/submit-scam-report', [HomeController::class, 'submitScamReport'])->name('submit.scam.report');
Route::post('/like-scam-report/{id}', [HomeController::class, 'likeScamReport'])->name('like.scam.report');
Route::get('/transaction/status/{id}', [HomeController::class, 'transactionStatus'])->name('transaction.status');
Route::get('/get-access-token', [HomeController::class, 'getAccessToken'])->name('get.access.token');
Route::post('/create-otp', [HomeController::class, 'createOTP'])->name('create.otp');
Route::get('/api/documentation', [HomeController::class, 'getAPIDocumentation'])->name('api-documentation');

Route::get('/e-contract', [HomeController::class, 'getEContract'])->name('e-contract');
Route::get('/e-contract-print/{transactionID}', [ContractController::class, 'generateEscrowPdf'])->name('e-contract.print');
Route::view('/thank-you', 'process.thank-you')->name('thank.you');
Route::get('/livechat/start/{transactionId}', [LiveChatController::class, 'start'])->name('livechat.start');
Route::get('/livechat/admin/{token}', [LiveChatController::class, 'showAdmin'])->name('livechat.admin');
Route::get('/livechat/{token}', [LiveChatController::class, 'showUser'])->name('livechat.user');
Route::get('/livechat/{token}/messages', [LiveChatController::class, 'messages'])->name('livechat.messages');
Route::post('/livechat/{token}/send', [LiveChatController::class, 'send'])->name('livechat.send');
Route::post('/livechat/{token}/typing', [LiveChatController::class, 'typing'])->name('livechat.typing');

// Test SMS route - Send SMS to +254723014032
Route::get('/test-sms', [HomeController::class, 'testSms'])->name('test.sms');
Route::get('/test-sms/{phone}', [HomeController::class, 'testSms'])->name('test.sms.phone');

Route::post('/approve-transaction-post/{id}', [HomeController::class, 'approveTransactionPost'])->name('transaction.approve');
Route::post('/custom-login', [DashboardController::class, 'customLogin'])->name('custom.login');

Auth::routes();

Route::middleware('guest')->group(function () {
    Route::get('/developer/login', [LoginController::class, 'showDeveloperLoginForm'])->name('developer.login');
    Route::post('/developer/login', [LoginController::class, 'developerLogin'])
        ->middleware('throttle:12,1')
        ->name('developer.login.submit');

    Route::get('/developer/register', [RegisterController::class, 'showDeveloperRegistrationForm'])->name('developer.register');
    Route::post('/developer/register', [RegisterController::class, 'registerDeveloper'])
        ->middleware('throttle:12,1')
        ->name('developer.register.submit');

    Route::post('/login/phone/send-otp', [PhoneOtpLoginController::class, 'sendOtp'])
        ->middleware('throttle:5,1')
        ->name('login.phone.send-otp');
    Route::post('/login/phone', [PhoneOtpLoginController::class, 'verify'])
        ->middleware('throttle:12,1')
        ->name('login.phone');
    Route::get('/login/phone/cancel', [PhoneOtpLoginController::class, 'cancel'])
        ->name('login.phone.cancel');

    Route::post('/register/phone/send-otp', [PhoneOtpRegisterController::class, 'sendOtp'])
        ->middleware('throttle:5,1')
        ->name('register.phone.send-otp');
    Route::post('/register/phone', [PhoneOtpRegisterController::class, 'verify'])
        ->middleware('throttle:12,1')
        ->name('register.phone');
    Route::get('/register/phone/cancel', [PhoneOtpRegisterController::class, 'cancel'])
        ->name('register.phone.cancel');
});

// DashboardController routes

Route::get('/home', [DashboardController::class, 'index'])->name('home.dashboard');
Route::get('/view/{id}', [DashboardController::class, 'viewTransaction'])->name('view.transaction');
Route::post('/approve-transaction/{id}', [DashboardController::class, 'approveTransaction'])->name('transaction.approves');
Route::post('/reject-transaction/{id}', [DashboardController::class, 'rejectTransaction'])->name('reject.transaction');
Route::get('/transaction.export', [DashboardController::class, 'exportTransactions'])->name('transaction.export');

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
    Route::get('/dashboard/create-transaction', [DashboardController::class, 'createTransaction'])->name('user.dashboard.create');
    Route::post('/user/update', [DashboardController::class, 'update'])->name('user.update');
    Route::post('/user/update-password', [DashboardController::class, 'updatePassword'])->name('user.update-password');
});

Route::middleware('auth')->group(function () {
    Route::get('/developer', [DeveloperController::class, 'index'])->name('api.home');
    Route::post('/developer/api-key', [DeveloperController::class, 'generateOrRegenerateKey'])->name('api.key.regenerate');
});
/*------------------------------------------
--------------------------------------------
Admin panel (dedicated `admin` guard)
--------------------------------------------
--------------------------------------------*/
Route::prefix('admin')->name('admin.')->group(function () {
    Route::middleware('guest:admin')->group(function () {
        Route::get('login', [AdminAuthController::class, 'showLoginForm'])->name('login');
        Route::post('login', [AdminAuthController::class, 'login'])
            ->middleware('throttle:8,1')
            ->name('login.post');
    });
    Route::post('logout', [AdminAuthController::class, 'logout'])->middleware('auth:admin')->name('logout');
    Route::middleware('auth:admin')->group(function () {
        Route::get('/', function () {
            return redirect()->route('admin.dashboard');
        })->name('index');
        Route::get('dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
        Route::get('transactions/export', [AdminTransactionController::class, 'export'])->name('transactions.export');
        Route::resource('transactions', AdminTransactionController::class)->only(['index', 'show', 'destroy']);
        Route::get('business', [BusinessController::class, 'index'])->name('business.index');
        Route::get('mpesa-transactions', [MpesaTransactionsController::class, 'index'])->name('mpesa-transactions.index');
        Route::get('mpesa-transactions/b2c/{mpesa_b2c}', [MpesaTransactionsController::class, 'showB2c'])->name('mpesa-transactions.b2c.show');
        Route::post('mpesa-transactions/b2c/{mpesa_b2c}/approve', [MpesaTransactionsController::class, 'approveB2c'])->name('mpesa-transactions.b2c.approve');
        Route::post('mpesa-transactions/b2c/{mpesa_b2c}/reject', [MpesaTransactionsController::class, 'rejectB2c'])->name('mpesa-transactions.b2c.reject');
        Route::post('mpesa-transactions/b2b/{mpesa_b2b}/approve', [MpesaTransactionsController::class, 'approveB2b'])->name('mpesa-transactions.b2b.approve');
        Route::post('mpesa-transactions/b2b/{mpesa_b2b}/reject', [MpesaTransactionsController::class, 'rejectB2b'])->name('mpesa-transactions.b2b.reject');
        Route::get('api-access', [ApiAccessController::class, 'index'])->name('api-access.index');
        Route::post('api-access/{user}/regenerate-key', [ApiAccessController::class, 'regenerateKey'])->name('api-access.regenerate');
        Route::resource('users', AdminUserController::class)->only(['index', 'show']);
        Route::get('live-chats', [AdminLiveChatController::class, 'index'])->name('live-chats.index');
        Route::get('live-chats/{liveChat}', [AdminLiveChatController::class, 'show'])->name('live-chats.show');
        Route::get('live-chats/{liveChat}/messages', [AdminLiveChatController::class, 'messages'])->name('live-chats.messages');
        Route::post('live-chats/{liveChat}/send', [AdminLiveChatController::class, 'send'])->name('live-chats.send');
        Route::post('live-chats/{liveChat}/typing', [AdminLiveChatController::class, 'typing'])->name('live-chats.typing');
        Route::get('disputes', [AdminDisputeController::class, 'index'])->name('disputes.index');
        Route::post('disputes/{dispute}/status', [AdminDisputeController::class, 'updateStatus'])->name('disputes.status');
        Route::resource('pages', AdminPageController::class);
        Route::resource('support-help-items', SupportHelpItemController::class)->except(['show']);
        Route::get('site-settings', [SiteSettingsController::class, 'edit'])->name('site-settings.edit');
        Route::put('site-settings', [SiteSettingsController::class, 'update'])->name('site-settings.update');
        Route::post('site-settings/reset', [SiteSettingsController::class, 'reset'])->name('site-settings.reset');
        Route::get('legal-pages', [LegalPageController::class, 'index'])->name('legal-pages.index');
        Route::get('legal-pages/{slug}/edit', [LegalPageController::class, 'edit'])
            ->name('legal-pages.edit')
            ->where('slug', 'terms-and-conditions|privacy-policy|security|complience');
        Route::get('contact', [ContactSubmissionController::class, 'index'])->name('contact.index');
        Route::get('contact/{contact}', [ContactSubmissionController::class, 'show'])->name('contact.show');
        Route::post('contact/{contact}/unread', [ContactSubmissionController::class, 'markUnread'])->name('contact.unread');
        Route::get('scam-reports', [AdminScamReportController::class, 'index'])->name('scam-reports.index');
        Route::get('scam-reports/{scam_report}/evidence/{index}', [AdminScamReportController::class, 'evidence'])
            ->name('scam-reports.evidence')
            ->whereNumber('index');
        Route::post('scam-reports/{scam_report}/evidence', [AdminScamReportController::class, 'appendEvidence'])
            ->name('scam-reports.evidence.store');
        Route::get('scam-reports/{scam_report}', [AdminScamReportController::class, 'show'])->name('scam-reports.show');
        Route::post('scam-reports/{scam_report}/status', [AdminScamReportController::class, 'updateStatus'])->name('scam-reports.status');
        Route::get('sms-logs', [SmsLogController::class, 'index'])->name('sms-logs.index');
        Route::get('activity-logs', [ActivityLogController::class, 'index'])->name('activity-logs.index');
    });
});
