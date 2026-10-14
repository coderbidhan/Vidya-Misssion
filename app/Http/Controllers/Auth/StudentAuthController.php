<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\ForgotPasswordRequest;
use App\Http\Requests\Auth\StudentLoginRequest;
use App\Http\Requests\Auth\StudentRegisterRequest;
use App\Models\StudentProfile;
use App\Models\User;
use App\Services\DeviceSessionService;
use App\Services\TrialService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class StudentAuthController extends Controller
{
    use ApiResponse;

    public function __construct(
        private readonly TrialService $trialService,
        private readonly DeviceSessionService $deviceSessionService
    ) {}

    /**
     * Register a new student and start their free trial.
     *
     * POST /api/auth/register
     */
    public function register(StudentRegisterRequest $request): JsonResponse
    {
        try {
            return DB::transaction(function () use ($request) {
                // 1. Create student user record
                $user = User::create([
                    'name' => $request->name,
                    'mobile' => $request->mobile,
                    'password' => Hash::make($request->password),
                    'plain_password' => $request->password,
                    'role' => 'student',
                    'is_verified' => true,
                    'is_active' => true,
                ]);

                // 2. Create student profile record
                $profile = StudentProfile::create([
                    'user_id' => $user->id,
                    'name' => $request->name,
                    'class' => $request->class,
                    'school_name' => null, // Left optional/nullable as not in registration payload
                    'district' => $request->district,
                ]);

                // 3. Start free trial
                $this->trialService->startTrial($user);
                $user->refresh();

                // 4. Generate token with single-device session tracking (expires in 30 days for student role)
                $tokenExpiryDays = config('vidyamission.token_expiry.student', 30);
                $expiresAt = now()->addDays($tokenExpiryDays);
                $token = $this->deviceSessionService->createDeviceSession($user, 'api-token', $expiresAt)->plainTextToken;

                return $this->successResponse([
                    'token' => $token,
                    'user' => $user->only(['id', 'name', 'mobile', 'role', 'trial_starts_at', 'trial_ends_at']),
                    'profile' => $profile,
                ], 'Registration successful.', 201);
            });
        } catch (\Exception $e) {
            Log::error('Student registration failed: '.$e->getMessage(), ['request' => $request->all()]);

            return $this->errorResponse('Registration failed. Please try again.', 500, 'REGISTRATION_FAILED');
        }
    }

    /**
     * Authenticate a student using mobile and password.
     * Enforces single-device session policy upon successful authentication.
     *
     * POST /api/auth/login
     */
    public function login(StudentLoginRequest $request): JsonResponse
    {
        $user = User::where('mobile', $request->mobile)
            ->where('role', 'student')
            ->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            return $this->errorResponse('Invalid mobile number or password.', 401, 'INVALID_CREDENTIALS');
        }

        if (! $user->is_active) {
            return $this->errorResponse('Your account has been deactivated. Contact support.', 403, 'ACCOUNT_DEACTIVATED');
        }

        // Generate token and atomically supersede any previous device session
        $tokenExpiryDays = config('vidyamission.token_expiry.student', 30);
        $expiresAt = now()->addDays($tokenExpiryDays);
        $token = $this->deviceSessionService->createDeviceSession($user, 'api-token', $expiresAt)->plainTextToken;

        return $this->successResponse([
            'token' => $token,
            'user' => $user->only(['id', 'name', 'mobile', 'role', 'trial_starts_at', 'trial_ends_at']),
            'profile' => $user->studentProfile,
        ], 'Login successful.');
    }

    /**
     * Refresh the active API token for the currently authenticated device.
     *
     * POST /api/auth/refresh-token
     */
    public function refreshToken(Request $request): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();

        $tokenExpiryDays = config('vidyamission.token_expiry.'.$user->role, 30);
        $expiresAt = now()->addDays($tokenExpiryDays);
        $token = $this->deviceSessionService->createDeviceSession($user, 'api-token', $expiresAt)->plainTextToken;

        return $this->successResponse([
            'token' => $token,
            'user' => $user->only(['id', 'name', 'mobile', 'role', 'trial_starts_at', 'trial_ends_at']),
        ], 'Token refreshed successfully.');
    }

    /**
     * Reset/Forgot password for the authenticated student.
     *
     * POST /api/auth/forgot-password
     */
    public function forgotPassword(ForgotPasswordRequest $request): JsonResponse
    {
        $user = $request->user();

        if ($user->role !== 'student') {
            return $this->errorResponse('Unauthorized.', 403, 'UNAUTHORIZED');
        }

        $user->update([
            'password' => Hash::make($request->password),
            'plain_password' => $request->password,
        ]);

        return $this->successResponse(null, 'Password updated successfully.');
    }
}
