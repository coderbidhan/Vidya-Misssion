<?php

namespace App\Http\Middleware;

use App\Models\User;
use App\Services\DeviceSessionService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Sanctum\PersonalAccessToken;
use Laravel\Sanctum\TransientToken;
use Symfony\Component\HttpFoundation\Response;

class EnsureSingleDeviceSession
{
    public function __construct(private readonly DeviceSessionService $deviceSessionService) {}

    /**
     * Handle an incoming request.
     * Enforce single-device session policy for authenticated users.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // 1. Direct Bearer token check for superseded device tokens
        $bearer = $request->bearerToken();
        if ($bearer && str_contains($bearer, '|')) {
            [$bearerId] = explode('|', $bearer, 2);
            if (ctype_digit($bearerId) && $this->deviceSessionService->isTokenSuperseded((int) $bearerId)) {
                if (Auth::guard('sanctum')->check()) {
                    Auth::guard('sanctum')->forgetUser();
                }

                return response()->json([
                    'success' => false,
                    'message' => 'Your account has been logged in on another device. Please log in again.',
                    'code' => 'SESSION_REPLACED',
                ], Response::HTTP_UNAUTHORIZED);
            }
        }

        // 2. Ensure guard freshness when bearer token differs from cached user token (e.g. in test runs)
        $user = $request->user();
        if ($user && $bearer && str_contains($bearer, '|')) {
            [$bearerId] = explode('|', $bearer, 2);
            $currentToken = $user->currentAccessToken();
            if ($currentToken && ! ($currentToken instanceof TransientToken) && (int) $currentToken->id !== (int) $bearerId) {
                Auth::guard('sanctum')->forgetUser();
                $user = Auth::guard('sanctum')->user();
                $request->setUserResolver(fn () => $user);
            }
        }

        if (! $user) {
            return $next($request);
        }

        // 3. Deactivated account check
        if ($user->is_active === false) {
            return response()->json([
                'success' => false,
                'message' => 'Your account has been deactivated. Contact support.',
                'code' => 'ACCOUNT_DEACTIVATED',
            ], Response::HTTP_FORBIDDEN);
        }

        $currentToken = $user->currentAccessToken();

        // 4. Bypass check for Sanctum TransientToken (mocked tokens in automated testing)
        if ($currentToken instanceof TransientToken) {
            return $next($request);
        }

        // 5. If no personal access token instance exists, delegate to next middleware
        if (! $currentToken || ! isset($currentToken->id)) {
            return $next($request);
        }

        $tokenId = (int) $currentToken->id;

        // Check if token was deleted from database (e.g. logged out)
        if ($currentToken->exists === false || ! PersonalAccessToken::where('id', $tokenId)->exists()) {
            if (Auth::guard('sanctum')->check()) {
                Auth::guard('sanctum')->forgetUser();
            }

            return response()->json([
                'success' => false,
                'message' => 'Unauthenticated.',
            ], Response::HTTP_UNAUTHORIZED);
        }

        // 6. Check if token was marked superseded
        if ($this->deviceSessionService->isTokenSuperseded($tokenId)) {
            $currentToken->delete();

            return response()->json([
                'success' => false,
                'message' => 'Your account has been logged in on another device. Please log in again.',
                'code' => 'SESSION_REPLACED',
            ], Response::HTTP_UNAUTHORIZED);
        }

        // 7. Backward compatibility for pre-existing tokens: adopt if not set
        if ($user->current_device_token_id === null) {
            $user->update(['current_device_token_id' => $tokenId]);

            return $next($request);
        }

        // 8. Authoritative check: ensure presented token ID matches active device session
        if (! $this->deviceSessionService->isTokenActive($user, $tokenId)) {
            $currentToken->delete();

            return response()->json([
                'success' => false,
                'message' => 'Your account has been logged in on another device. Please log in again.',
                'code' => 'SESSION_REPLACED',
            ], Response::HTTP_UNAUTHORIZED);
        }

        return $next($request);
    }
}
