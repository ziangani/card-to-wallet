<?php

use Illuminate\Support\Facades\Route;


//redirect /login to /merchant/login?from=old_url
Route::get('/login', function () {
    return redirect('/merchant/login?from=old_url');
});


//onboarding
Route::get('/', [\App\Http\Controllers\Frontend\WebSiteController::class, 'index'])->name('home');

//payments
Route::get('/checkout/{token}', [\App\Http\Controllers\Frontend\CheckOutController::class, 'checkout'])->name('checkout');
Route::post('/checkout/{token}/process', [\App\Http\Controllers\Frontend\CheckOutController::class, 'processCheckout'])->name('checkout.process');
Route::get('/checkout/{token}/status', [\App\Http\Controllers\Frontend\CheckOutController::class, 'getStatus'])->name('checkout.status');
Route::get('/checkout/{token}/mpgs/status', [\App\Http\Controllers\Frontend\CheckOutController::class, 'mpgsStatus'])->name('checkout.mpgs.status');
Route::get('/checkout/{token}/return', [\App\Http\Controllers\Frontend\CheckOutController::class, 'return'])->name('checkout.return');

//Payment links
Route::get('/tpm/{merchantcode}/', [\App\Http\Controllers\Frontend\PaymentLinkController::class, 'merchantPayLink'])->name('paymentlink.merchant');
Route::post('/tpm/{merchantcode}/process', [\App\Http\Controllers\Frontend\PaymentLinkController::class, 'makeMerchantToken'])->name('paymentlink.create_merchant_token');
