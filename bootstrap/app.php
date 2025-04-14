<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {

        $middleware->validateCsrfTokens(except: [
            'admissions/submit',
            'admissions/uploads/validate',
            'tpm/*',
            '/checkout/*/process',
            'checkout/*/3ds/callback'
        ]);
        
        // Register middleware aliases
        $middleware->alias([
            'corporate.access' => \App\Http\Middleware\CorporateAccess::class,
            'corporate.role' => \App\Http\Middleware\CorporateRoleCheck::class,
            'approval.required' => \App\Http\Middleware\ApprovalRequired::class,
            'retail.access' => \App\Http\Middleware\RetailAccess::class,
        ]);
        
        //$middleware->append(\App\Http\Middleware\AuthenticateMerchant::class);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
