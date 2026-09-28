<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\SendOtpRequest;
use App\Http\Requests\Auth\VerifyOtpRequest;
use App\Models\User;
use App\Services\OtpService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class OtpController extends Controller
{
    use ApiResponse;

    public function __construct(private readonly OtpService $otpService) {}

    /**
     * Send a 6-digit OTP to the provided email or mobile.
     * Creates the user account if it does not exist yet.
     *
     * POST /api/auth/send-otp
     */
    public function sendOtp(SendOtpRequest $request): JsonResponse
    {
        try {
            $identifier = $request->email ?: $request->mobile;
            $search = $request->email ? ['email' => $request->email] : ['mobile' => $request->mobile];

            $user = User::firstOrCreate(
                $search,
                ['role' => 'student', 'is_active' => true]
            );

            if (! $user->is_active) {
                return $this->errorResponse('Your account has been deactivated. Contact support.', 403, 'ACCOUNT_DEACTIVATED');
            }

            $this->otpService->sendOtp($user);

            return $this->successResponse(null, 'OTP sent successfully. Valid for '.config('vidyamission.otp_expiry_minutes', 10).' minutes.');
        } catch (\Exception $e) {
            Log::warning('OTP send failed: '.$e->getMessage(), ['request' => $request->all()]);

            return $this->errorResponse($e->getMessage(), 429, 'RATE_LIMITED');
        }
    }

    /**
     * Verify the OTP and issue a Sanctum API token.
     *
     * POST /api/auth/verify-otp
     */
    public function verifyOtp(VerifyOtpRequest $request): JsonResponse
    {
        $user = $request->email
            ? User::where('email', $request->email)->first()
            : User::where('mobile', $request->mobile)->first();

        if (! $user || ! $this->otpService->verifyOtp($user, $request->otp)) {
            return $this->errorResponse('Invalid or expired OTP.', 401, 'INVALID_OTP');
        }

        // Mark as verified and clear OTP
        $user->update([
            'is_verified' => true,
            'otp' => null,
            'otp_expires_at' => null,
        ]);

        $needsProfile = $user->trial_starts_at === null;

        // Token expiry based on role
        $tokenExpiryDays = config('vidyamission.token_expiry.'.$user->role, 30);
        $expiresAt = now()->addDays($tokenExpiryDays);

        $token = $user->createToken('api-token', ['*'], $expiresAt)->plainTextToken;

        return $this->successResponse([
            'token' => $token,
            'user' => $user->only(['id', 'name', 'email', 'mobile', 'role', 'trial_ends_at']),
            'needs_profile' => $needsProfile,
        ], 'Login successful.');
    }

    /**
     * Revoke the current API token (logout).
     *
     * POST /api/auth/logout
     */
    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()?->delete();

        return $this->successResponse(null, 'Logged out successfully.');
    }
}
