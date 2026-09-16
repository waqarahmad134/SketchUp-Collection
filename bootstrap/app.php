<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Remove EnsureFrontendRequestsAreStateful from API routes
        // This middleware enables CSRF protection, which we don't need for token-based API auth
        // API routes should use token authentication, not cookie-based SPA authentication

        // Payment providers POST back to these URLs without a CSRF token
        $middleware->validateCsrfTokens(except: [
            'payment/callback/*',
            'payment/webhook/*',
        ]);

        $middleware->alias([
            'verified' => \App\Http\Middleware\EnsureEmailIsVerified::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
