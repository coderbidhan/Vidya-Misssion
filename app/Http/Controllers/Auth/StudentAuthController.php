<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\ForgotPasswordRequest;
use App\Http\Requests\Auth\StudentLoginRequest;
use App\Http\Requests\Auth\StudentRegisterRequest;
use App\Models\StudentProfile;
use App\Models\User;
use App\Services\TrialService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class StudentAuthController extends Controller
{
    use ApiResponse;

    public function __construct(private readonly TrialService $trialService) {}

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
                    'email' => $request->email,
                    'password' => Hash::make($request->password),
                    'role' => 'student',
                    'is_verified' => true,
                    'is_active' => true,
                ]);

                // 2. Create student profile record
                $profile = StudentProfile::create([
                    'user_id' => $user->id,
                    'name' => $request->name,
                    'class' => $request->class,
                    'school_name' => $request->school_name,
                    'district' => $request->district,
                ]);

                // 3. Start free trial
                $this->trialService->startTrial($user);
                $user->refresh();

                // 4. Generate token (expires in 30 days for student role)
                $tokenExpiryDays = config('vidyamission.token_expiry.student', 30);
                $expiresAt = now()->addDays($tokenExpiryDays);
                $token = $user->createToken('api-token', ['*'], $expiresAt)->plainTextToken;

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
     * Authenticate a student using mobile/email and password.
     *
     * POST /api/auth/login
     */
    public function login(StudentLoginRequest $request): JsonResponse
    {
        $login = $request->input('mobile') ?: ($request->input('login') ?: $request->input('email'));

        $user = User::where(function ($query) use ($login) {
            $query->where('mobile', $login)->orWhere('email', $login);
        })
            ->where('role', 'student')
            ->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            return $this->errorResponse('Invalid mobile number or password.', 401, 'INVALID_CREDENTIALS');
        }

        if (! $user->is_active) {
            return $this->errorResponse('Your account has been deactivated. Contact support.', 403, 'ACCOUNT_DEACTIVATED');
        }

        // Single device login enforcement: revoke all previous tokens so previous device is logged out
        $user->tokens()->delete();

        // Generate token
        $tokenExpiryDays = config('vidyamission.token_expiry.student', 30);
        $expiresAt = now()->addDays($tokenExpiryDays);
        $token = $user->createToken('api-token', ['*'], $expiresAt)->plainTextToken;

        return $this->successResponse([
            'token' => $token,
            'user' => $user->only(['id', 'name', 'mobile', 'role', 'trial_starts_at', 'trial_ends_at']),
            'profile' => $user->studentProfile,
        ], 'Login successful.');
    }

    /**
     * Reset/Forgot password for a student.
     *
     * POST /api/auth/forgot-password
     */
    public function forgotPassword(ForgotPasswordRequest $request): JsonResponse
    {
        $user = $request->user();

        if (! $user) {
            $identifier = $request->mobile ?: $request->email;
            if (! $identifier) {
                return $this->errorResponse('Mobile number or email is required to reset password.', 422, 'MISSING_IDENTIFIER');
            }

            $user = User::where('mobile', $identifier)
                ->orWhere('email', $identifier)
                ->first();
        }

        if (! $user) {
            return $this->errorResponse('Account not found with provided details.', 404, 'USER_NOT_FOUND');
        }

        $user->update([
            'password' => Hash::make($request->password),
        ]);

        return $this->successResponse(null, 'Password updated successfully.');
    }
}
