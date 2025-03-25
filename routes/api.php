<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\HostedCheckOutApiController;
use App\Http\Controllers\API\TerminalApiController;

// New terminal routes
Route::post('terminal/initialize', [TerminalApiController::class, 'initializeParameters']);
Route::post('terminal/heartbeat', [TerminalApiController::class, 'heartbeat']);

Route::middleware(\App\Http\Middleware\AuthenticateMerchant::class)->group(function () {
    // Old routes
    Route::post('merchant/transactions', [HostedCheckOutApiController::class, 'history']);
    Route::post('hc/gettoken', [HostedCheckOutApiController::class, 'getToken']);
    Route::post('hc/statuscheck', [HostedCheckOutApiController::class, 'getStatus']);
    Route::post('ic/pay/mobilemoney', [HostedCheckOutApiController::class, 'payWithMobileMoney']);
    Route::post('ic/pay/mobilemoney/instant', [HostedCheckOutApiController::class, 'generateTokenAndSendMobileMoneyRequest']);

});

Route::get('/test', function () {
    return response()->json(['message' => 'Hello World!'], 200);
});
