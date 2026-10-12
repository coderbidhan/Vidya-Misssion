<?php

use App\Http\Middleware\AdminWebAuth;
use App\Http\Middleware\CheckRole;
use App\Http\Middleware\CheckTrialOrSubscription;
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
        // Sanctum stateful domains (for SPA / cookie-based auth)
        $middleware->statefulApi();

        // Register custom middleware aliases
        $middleware->alias([
            'trial_check' => CheckTrialOrSubscription::class,
            'role' => CheckRole::class,
            'admin_auth' => AdminWebAuth::class,
        ]);

        // Exclude API routes from CSRF since we use Bearer tokens for API auth
        $middleware->validateCsrfTokens(except: [
            'api/*',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
