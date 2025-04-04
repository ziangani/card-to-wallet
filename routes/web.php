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
    Route::get('/email/verify/{id}/{hash}', [App\Http\Controllers\Auth\VerifyEmailController::class, '__invoke'])
        ->middleware(['signed', 'throttle:6,1'])
        ->name('verification.verify');
});

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
});
