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

        // Admin and teachers bypass this check
        if ($user->isAdmin() || $user->isTeacher()) {
            return $next($request);
        }

        // Step 1: Active subscription grant full content access for validity period
        if ($user->hasActiveSubscription()) {
            return $next($request);
        }

        // Step 2: If no active subscription, initialize trial if not yet started
        if (! $user->trial_starts_at && ! $user->trial_ends_at) {
            if (! $user->studentProfile) {
                return response()->json([
                    'success' => false,
                    'message' => 'Complete your profile first.',
                    'code' => 'PROFILE_INCOMPLETE',
                ], Response::HTTP_FORBIDDEN);
            }
            $user->update([
                'trial_starts_at' => now(),
                'trial_ends_at' => now()->addDays(config('vidyamission.trial_days', 3)),
            ]);
        }

        // Step 3: Still within the free trial window
        if ($user->isInTrial()) {
            return $next($request);
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
