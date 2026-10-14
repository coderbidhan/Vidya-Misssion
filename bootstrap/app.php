<?php

use App\Http\Middleware\AdminWebAuth;
use App\Http\Middleware\CheckRole;
use App\Http\Middleware\CheckTrialOrSubscription;
use App\Http\Middleware\EnsureSingleDeviceSession;
use App\Services\DeviceSessionService;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

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
            'single_device' => EnsureSingleDeviceSession::class,
        ]);

        // Exclude API routes from CSRF since we use Bearer tokens for API auth
        $middleware->validateCsrfTokens(except: [
            'api/*',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (AuthenticationException $e, Request $request) {
            if ($request->expectsJson() || $request->is('api/*')) {
                $bearer = $request->bearerToken();
                if ($bearer && str_contains($bearer, '|')) {
                    [$tokenId] = explode('|', $bearer, 2);
                    if (ctype_digit($tokenId) && app(DeviceSessionService::class)->isTokenSuperseded((int) $tokenId)) {
                        return response()->json([
                            'success' => false,
                            'message' => 'Your account has been logged in on another device. Please log in again.',
                            'code' => 'SESSION_REPLACED',
                        ], 401);
                    }
                }

                return response()->json([
                    'success' => false,
                    'message' => 'Unauthenticated.',
                ], 401);
            }
        });
    })->create();
