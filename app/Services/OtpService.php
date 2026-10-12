<?php

namespace App\Services;

use App\Jobs\SendOtpEmail;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;

class OtpService
{
    /**
     * Generate a cryptographically random 6-digit OTP.
     */
    public function generateOtp(): string
    {
        return str_pad((string) random_int(100000, 999999), 6, '0', STR_PAD_LEFT);
    }

    /**
     * Generate and store OTP for the user, then dispatch the email job.
     *
     * Rate limit: max 3 OTPs per email per hour.
     *
     * @throws \Exception if rate limit exceeded
     */
    public function sendOtp(User $user): void
    {
        $rateLimitKey = 'otp_rate_limit:'.$user->email;
        $limit = config('vidyamission.otp_rate_limit', 3);

        // Increment rate limit counter
        $attempts = Cache::get($rateLimitKey, 0);

        if ($attempts >= $limit) {
            throw new \Exception('Too many OTP requests. Please wait an hour before trying again.');
        }

        Cache::put($rateLimitKey, $attempts + 1, now()->addHour());

        // Generate and store hashed OTP
        $otp = $this->generateOtp();
        $expiryMinutes = config('vidyamission.otp_expiry_minutes', 10);

        $user->update([
            'otp' => Hash::make($otp),
            'otp_expires_at' => now()->addMinutes($expiryMinutes),
        ]);

        // Dispatch email job with the plain OTP (before hashing)
        SendOtpEmail::dispatch($user, $otp);
    }

    /**
     * Verify that the submitted OTP matches the stored hash and is not expired.
     */
    public function verifyOtp(User $user, string $otp): bool
    {
        if (! $user->otp || ! $user->otp_expires_at) {
            return false;
        }

        if (now()->isAfter($user->otp_expires_at)) {
            return false;
        }

        return Hash::check($otp, $user->otp);
    }
}
