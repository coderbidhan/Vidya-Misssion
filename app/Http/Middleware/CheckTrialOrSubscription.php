<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckTrialOrSubscription
{
    /**
     * Handle an incoming request.
     *
     * Grants access if the user is within the free trial period OR
     * has an active (completed) subscription.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        /** @var User $user */
        $user = $request->user();

        if (! $user) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthenticated.',
            ], Response::HTTP_UNAUTHORIZED);
        }

        // Grant access if admin/teacher, within free trial window, or has active paid subscription
        if ($user->hasAccess()) {
            return $next($request);
        }

        // Profile must be completed first if trial hasn't started
        if (! $user->trial_starts_at) {
            return response()->json([
                'success' => false,
                'message' => 'Complete your profile first.',
                'code' => 'PROFILE_INCOMPLETE',
            ], Response::HTTP_FORBIDDEN);
        }

        // Deny access — trial expired, no active subscription
        return response()->json([
            'success' => false,
            'message' => 'Trial expired. Please subscribe to continue learning.',
            'code' => 'TRIAL_EXPIRED',
            'subscription_required' => true,
        ], Response::HTTP_FORBIDDEN);
    }
}
