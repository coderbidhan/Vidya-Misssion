<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Http\Requests\Student\SetupProfileRequest;
use App\Http\Requests\Student\UpdateProfileRequest;
use App\Models\StudentProfile;
use App\Services\TrialService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    use ApiResponse;

    public function __construct(private readonly TrialService $trialService) {}

    /**
     * Get the authenticated user's profile.
     *
     * GET /api/profile
     */
    public function show(Request $request): JsonResponse
    {
        $user = $request->user()->load('studentProfile');

        return $this->successResponse([
            'user' => $user,
            'profile' => $user->studentProfile,
            'is_in_trial' => $user->isInTrial(),
            'is_trial_expired' => $user->isTrialExpired(),
            'has_active_subscription' => $user->hasActiveSubscription(),
            'has_access' => $user->hasAccess(),
        ]);
    }

    /**
     * Complete the student profile and start the free trial.
     *
     * POST /api/profile/setup
     */
    public function setup(SetupProfileRequest $request): JsonResponse
    {
        $user = $request->user();

        if ($user->trial_starts_at !== null) {
            return $this->errorResponse('Profile has already been set up.', 409, 'ALREADY_SETUP');
        }

        $profile = StudentProfile::create([
            'user_id' => $user->id,
            'name' => $request->name,
            'class' => $request->class,
            'school_name' => $request->school_name,
            'district' => $request->district,
        ]);

        // Update the user's name and start the trial
        $user->update(['name' => $request->name]);
        $this->trialService->startTrial($user);
        $user->refresh();

        return $this->successResponse([
            'user' => $user->only(['id', 'name', 'email', 'role', 'trial_starts_at', 'trial_ends_at']),
            'profile' => $profile,
            'trial_ends_at' => $user->trial_ends_at,
        ], 'Profile set up! Your 3-day free trial has started.');
    }

    /**
     * Update the authenticated student's profile.
     *
     * PUT /api/profile/update
     */
    public function update(UpdateProfileRequest $request): JsonResponse
    {
        $user = $request->user();
        $profile = $user->studentProfile;

        if (! $profile) {
            return $this->errorResponse('Profile not found. Please complete profile setup first.', 404, 'NOT_FOUND');
        }

        $updateData = $request->only(['name', 'class', 'school_name', 'district']);

        // Handle avatar upload
        if ($request->hasFile('avatar')) {
            $path = $request->file('avatar')->store('avatars', 'public');
            $updateData['avatar_url'] = Storage::url($path);
        }

        $profile->update($updateData);

        if (isset($updateData['name'])) {
            $user->update(['name' => $updateData['name']]);
        }

        return $this->successResponse($profile->fresh(), 'Profile updated successfully.');
    }
}
