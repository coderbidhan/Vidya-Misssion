<?php

namespace App\Services;

use App\Models\User;

class TrialService
{
    /**
     * Start the free trial for a user.
     * Sets trial_starts_at to now and trial_ends_at based on config.
     */
    public function startTrial(User $user): void
    {
        $trialDays = config('vidyamission.trial_days', 3);

        $user->update([
            'trial_starts_at' => now(),
            'trial_ends_at' => now()->addDays($trialDays),
        ]);
    }

    /**
     * Check if the user is currently within the free trial window.
     */
    public function isInTrial(User $user): bool
    {
        return $user->isInTrial();
    }

    /**
     * Check if the user has an active paid subscription.
     */
    public function hasActiveSubscription(User $user): bool
    {
        return $user->hasActiveSubscription();
    }

    /**
     * Check if the user has access to content (trial OR active subscription).
     */
    public function hasAccess(User $user): bool
    {
        return $user->hasAccess();
    }
}
