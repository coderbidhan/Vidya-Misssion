<?php

namespace App\Http\Middleware;

use App\Services\DeviceSessionService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminWebAuth
{
    public function __construct(private readonly DeviceSessionService $deviceSessionService) {}

    /**
     * Protect admin web routes — redirect to login if no valid session token or if superseded.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $adminToken = session('admin_token');

        if (! $adminToken) {
            return redirect()->route('admin.login')->with('error', 'Please log in to access the admin panel.');
        }

        // Check if the admin's device session has been superseded by a login on another device
        if (str_contains($adminToken, '|')) {
            [$tokenId] = explode('|', $adminToken, 2);
            if (ctype_digit($tokenId) && $this->deviceSessionService->isTokenSuperseded((int) $tokenId)) {
                session()->forget(['admin_token', 'admin_user']);

                return redirect()->route('admin.login')->with('error', 'You have been logged out because your account was signed in on another device.');
            }
        }

        return $next($request);
    }
}
