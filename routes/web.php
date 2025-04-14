<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\BeneficiaryController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SupportController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Home routes
Route::get('/', [App\Http\Controllers\HomeController::class, 'index'])->name('welcome');
Route::get('/terms', [App\Http\Controllers\HomeController::class, 'terms'])->name('terms');
Route::get('/privacy', [App\Http\Controllers\HomeController::class, 'privacy'])->name('privacy');
Route::get('/about', [App\Http\Controllers\HomeController::class, 'about'])->name('about');
Route::get('/contact', [App\Http\Controllers\HomeController::class, 'contact'])->name('contact');
Route::post('/contact', [App\Http\Controllers\HomeController::class, 'submitContact'])->name('contact.submit');

// Authentication routes
Route::middleware('guest')->group(function () {
    // Login routes
    Route::get('/login', [App\Http\Controllers\Auth\LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [App\Http\Controllers\Auth\LoginController::class, 'login']);

    // Registration routes
    Route::get('/register', [App\Http\Controllers\Auth\RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [App\Http\Controllers\Auth\RegisterController::class, 'register']);
    Route::get('/corporate/register', [App\Http\Controllers\Auth\RegisterController::class, 'showCorporateRegistrationForm'])->name('corporate.register');
    Route::post('/corporate/register', [App\Http\Controllers\Auth\RegisterController::class, 'registerCorporate'])->name('register.corporate');

    // Password reset routes
    Route::get('/password/reset', [App\Http\Controllers\Auth\ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
    Route::post('/password/email', [App\Http\Controllers\Auth\ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
    Route::get('/password/reset/{token}', [App\Http\Controllers\Auth\ResetPasswordController::class, 'showResetForm'])->name('password.reset');
    Route::post('/password/reset', [App\Http\Controllers\Auth\ResetPasswordController::class, 'reset'])->name('password.update');
});

// Logout route
Route::post('/logout', [App\Http\Controllers\Auth\LoginController::class, 'logout'])->name('logout');

// Email verification routes
Route::middleware('auth')->group(function () {
    Route::get('/email/verify', [App\Http\Controllers\Auth\VerifyEmailController::class, 'notice'])->name('verification.notice');
});

// Email verification route that doesn't require authentication
Route::get('/email/verify/{id}/{hash}', [App\Http\Controllers\Auth\VerifyEmailController::class, 'verifyWithoutAuth'])
    ->middleware(['signed', 'throttle:6,1'])
    ->name('verification.verify');

// Verification routes
Route::middleware(['auth'])->group(function () {
    Route::get('/verify-phone', [App\Http\Controllers\VerificationController::class, 'showPhoneVerification'])->name('verification.phone');
    Route::post('/verify-phone/send', [App\Http\Controllers\VerificationController::class, 'sendPhoneVerificationCode'])->name('verification.phone.send');
    Route::post('/verify-phone', [App\Http\Controllers\VerificationController::class, 'verifyPhone'])->name('verification.phone.verify');
    Route::post('/email/verification-notification', [App\Http\Controllers\VerificationController::class, 'resendEmailVerification'])->name('verification.send');
    Route::post('/email/check-verification', [App\Http\Controllers\VerificationController::class, 'checkEmailVerification'])->name('verification.check');
});

// Protected routes
Route::middleware(['auth', 'verified'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Transaction routes
    Route::prefix('transactions')->name('transactions.')->group(function () {
        // Initiate transaction
        Route::get('/initiate', [TransactionController::class, 'initiate'])->name('initiate');
        Route::post('/initiate', [TransactionController::class, 'processInitiate'])->name('process-initiate');

        // Confirm transaction
        Route::get('/confirm', [TransactionController::class, 'confirm'])->name('confirm');
        Route::post('/confirm', [TransactionController::class, 'processConfirm'])->name('process-confirm');

        // Payment
        Route::get('/payment', [TransactionController::class, 'payment'])->name('payment');
        Route::post('/payment', [TransactionController::class, 'processPayment'])->name('process-payment');

        // MPGS Integration
        Route::post('/mpgs/checkout', [TransactionController::class, 'mpgsCheckout'])->name('mpgs.checkout');
        Route::get('/mpgs/callback/{uuid}', [TransactionController::class, 'mpgsCallback'])->name('mpgs.callback');
        Route::post('/process-ajax', [TransactionController::class, 'processAjax'])->name('process-ajax');

        // Success and failure
        Route::get('/success/{uuid}', [TransactionController::class, 'success'])->name('success');
        Route::get('/failure/{uuid}', [TransactionController::class, 'failure'])->name('failure');

        // Transaction history
        Route::get('/history', [TransactionController::class, 'history'])->name('history');
        Route::get('/show/{uuid}', [TransactionController::class, 'show'])->name('show');

        // Transaction actions
        Route::get('/download/{uuid}', [TransactionController::class, 'download'])->name('download');
        Route::post('/email-receipt/{uuid}', [TransactionController::class, 'emailReceipt'])->name('email-receipt');
        Route::get('/retry/{uuid}', [TransactionController::class, 'retry'])->name('retry');
        Route::get('/export', [TransactionController::class, 'export'])->name('export');
    });

// Beneficiary routes
Route::prefix('beneficiaries')->name('beneficiaries.')->group(function () {
    Route::get('/', [BeneficiaryController::class, 'index'])->name('index');
    Route::get('/create', [BeneficiaryController::class, 'create'])->name('create');
    Route::post('/', [BeneficiaryController::class, 'store'])->name('store');
    Route::put('/{id}', [BeneficiaryController::class, 'update'])->name('update');
    Route::delete('/{id}', [BeneficiaryController::class, 'destroy'])->name('destroy');
    Route::post('/{id}/toggle-favorite', [BeneficiaryController::class, 'toggleFavorite'])->name('toggle-favorite');
});

    // Profile routes
    Route::prefix('profile')->name('profile.')->group(function () {
        Route::get('/', [ProfileController::class, 'index'])->name('index');
        Route::put('/', [ProfileController::class, 'update'])->name('update');
        Route::get('/security', [ProfileController::class, 'security'])->name('security');
        Route::put('/password', [ProfileController::class, 'updatePassword'])->name('update-password');
        Route::get('/kyc', [ProfileController::class, 'kyc'])->name('kyc');
        Route::post('/kyc', [ProfileController::class, 'uploadKyc'])->name('upload-kyc');
    });

    // Support route
    Route::get('/support', [SupportController::class, 'index'])->name('support');
    Route::post('/support', [SupportController::class, 'submit'])->name('support.submit');
    Route::get('/faq', [SupportController::class, 'faq'])->name('faq');

    // Transaction routes
    Route::prefix('transactions')->name('transactions.')->group(function () {
        Route::post('/quick', [TransactionController::class, 'processQuick'])->name('quick');
    });

    // Corporate routes
    Route::prefix('corporate')->name('corporate.')->middleware(['auth', 'verified', 'corporate.access'])->group(function () {
        // Dashboard
        Route::get('/dashboard', [App\Http\Controllers\Corporate\CorporateController::class, 'index'])->name('dashboard');

        // Wallet routes
        Route::prefix('wallet')->name('wallet.')->group(function () {
            Route::get('/', [App\Http\Controllers\Corporate\CorporateWalletController::class, 'index'])->name('index');
            Route::get('/transactions', [App\Http\Controllers\Corporate\CorporateWalletController::class, 'transactions'])->name('transactions');
            Route::get('/deposit', [App\Http\Controllers\Corporate\CorporateWalletController::class, 'deposit'])->name('deposit');
            Route::post('/notify-deposit', [App\Http\Controllers\Corporate\CorporateWalletController::class, 'notifyDeposit'])->name('notify-deposit');
            Route::post('/process-card-deposit', [App\Http\Controllers\Corporate\CorporateWalletController::class, 'processCardDeposit'])->name('process-card-deposit');
            Route::get('/card-payment', [App\Http\Controllers\Corporate\CorporateWalletController::class, 'cardPayment'])->name('card-payment');
            Route::post('/mpgs-checkout', [App\Http\Controllers\Corporate\CorporateWalletController::class, 'mpgsCheckout'])->name('mpgs-checkout');
            Route::get('/card-callback/{uuid}', [App\Http\Controllers\Corporate\CorporateWalletController::class, 'cardCallback'])->name('card-callback');
        });

        // Disbursement routes
        Route::prefix('disbursements')->name('disbursements.')->group(function () {
            Route::get('/', [App\Http\Controllers\Corporate\BulkDisbursementController::class, 'index'])->name('index');
            Route::get('/create', [App\Http\Controllers\Corporate\BulkDisbursementController::class, 'create'])->name('create');
            Route::post('/validate', [App\Http\Controllers\Corporate\BulkDisbursementController::class, 'validateFile'])->name('validate');
            Route::get('/validate', [App\Http\Controllers\Corporate\BulkDisbursementController::class, 'showValidation'])->name('show-validation');
            Route::get('/review', [App\Http\Controllers\Corporate\BulkDisbursementController::class, 'review'])->name('review');
            Route::post('/submit', [App\Http\Controllers\Corporate\BulkDisbursementController::class, 'submit'])->name('submit');
            Route::get('/success', [App\Http\Controllers\Corporate\BulkDisbursementController::class, 'success'])->name('success');
            Route::get('/show/{id}', [App\Http\Controllers\Corporate\BulkDisbursementController::class, 'show'])->name('show');
            Route::get('/download-errors', [App\Http\Controllers\Corporate\BulkDisbursementController::class, 'downloadErrors'])->name('download-errors');
            Route::get('/template/{format}', [App\Http\Controllers\Corporate\BulkDisbursementController::class, 'downloadTemplate'])->name('template');
            Route::get('/get-validation-results', [App\Http\Controllers\Corporate\BulkDisbursementController::class, 'getValidationResults'])->name('get-validation-results');
        });

        // Approval routes
        Route::prefix('approvals')->name('approvals.')->group(function () {
            Route::get('/', [App\Http\Controllers\Corporate\ApprovalController::class, 'index'])->name('index');
            Route::get('/{id}', [App\Http\Controllers\Corporate\ApprovalController::class, 'show'])->name('show');
            Route::post('/{id}/approve', [App\Http\Controllers\Corporate\ApprovalController::class, 'approve'])->name('approve');
            Route::post('/{id}/reject', [App\Http\Controllers\Corporate\ApprovalController::class, 'reject'])->name('reject');
        });

        // User management routes
        Route::prefix('users')->name('users.')->group(function () {
            Route::get('/', [App\Http\Controllers\Corporate\CorporateUserController::class, 'index'])->name('index');
            Route::get('/invite', [App\Http\Controllers\Corporate\CorporateUserController::class, 'invite'])->name('invite');
            Route::post('/invite', [App\Http\Controllers\Corporate\CorporateUserController::class, 'processInvite'])->name('process-invite');
            Route::get('/{id}/edit', [App\Http\Controllers\Corporate\CorporateUserController::class, 'edit'])->name('edit');
            Route::put('/{id}', [App\Http\Controllers\Corporate\CorporateUserController::class, 'update'])->name('update');
        });

        // Reports routes
        Route::prefix('reports')->name('reports.')->group(function () {
            Route::get('/', [App\Http\Controllers\Corporate\CorporateReportController::class, 'index'])->name('index');
            Route::post('/generate', [App\Http\Controllers\Corporate\CorporateReportController::class, 'generate'])->name('generate');
            Route::get('/download/{id}', [App\Http\Controllers\Corporate\CorporateReportController::class, 'download'])->name('download');
        });

        // Settings routes
        Route::prefix('settings')->name('settings.')->group(function () {
            Route::get('/profile', [App\Http\Controllers\Corporate\CorporateSettingsController::class, 'profile'])->name('profile');
            Route::put('/profile', [App\Http\Controllers\Corporate\CorporateSettingsController::class, 'updateProfile'])->name('update-profile');
            Route::delete('/documents/{id}', [App\Http\Controllers\Corporate\CorporateSettingsController::class, 'deleteDocument'])->name('delete-document');
            Route::get('/security', [App\Http\Controllers\Corporate\CorporateSettingsController::class, 'security'])->name('security');
            Route::put('/password', [App\Http\Controllers\Corporate\CorporateSettingsController::class, 'updatePassword'])->name('update-password');
            Route::get('/roles', [App\Http\Controllers\Corporate\CorporateSettingsController::class, 'roles'])->name('roles');
            Route::put('/roles', [App\Http\Controllers\Corporate\CorporateSettingsController::class, 'updateRoles'])->name('update-roles');
            Route::get('/approvals', [App\Http\Controllers\Corporate\CorporateSettingsController::class, 'approvals'])->name('approvals');
            Route::put('/approvals', [App\Http\Controllers\Corporate\CorporateSettingsController::class, 'updateApprovals'])->name('update-approvals');
            Route::get('/rates', [App\Http\Controllers\Corporate\CorporateSettingsController::class, 'rates'])->name('rates');
        });
    });
});
