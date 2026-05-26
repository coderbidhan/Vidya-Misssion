<?php

namespace App\Http\Middleware;

use App\Models\UserSubscription;
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
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        /** @var \App\Models\User $user */
        $user = $request->user();

        // Admin and teachers bypass this check
        if ($user->role === 'admin' || $user->role === 'teacher') {
            return $next($request);
        }

        // Step 1: Profile must be completed first (trial_starts_at set on profile completion)
        if (! $user->trial_starts_at) {
            return response()->json([
                'success' => false,
                'message' => 'Complete your profile first.',
                'code'    => 'PROFILE_INCOMPLETE',
            ], Response::HTTP_FORBIDDEN);
        }

        // Step 2: Still within the free trial window
        if ($user->trial_ends_at && now()->lessThanOrEqualTo($user->trial_ends_at)) {
            return $next($request);
        }

        // Step 3: Check for an active paid subscription
        $hasActiveSubscription = $user->userSubscriptions()
            ->where('payment_status', 'completed')
            ->where('starts_at', '<=', now())
            ->where('ends_at', '>=', now())
            ->exists();

        if ($hasActiveSubscription) {
            return $next($request);
        }

        // Deny access — trial expired, no subscription
        return response()->json([
            'success'               => false,
            'message'               => 'Trial expired. Please subscribe to continue learning.',
            'code'                  => 'TRIAL_EXPIRED',
            'subscription_required' => true,
        ], Response::HTTP_FORBIDDEN);
    }
}
