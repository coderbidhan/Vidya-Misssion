<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminWebAuth
{
    /**
     * Protect admin web routes — redirect to login if no valid session token.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! session('admin_token')) {
            return redirect()->route('admin.login')->with('error', 'Please log in to access the admin panel.');
        }

        return $next($request);
    }
}
