<?php

namespace App\Services;

use App\Models\SupersededDeviceToken;
use App\Models\User;
use DateTimeInterface;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Laravel\Sanctum\NewAccessToken;

class DeviceSessionService
{
    /**
     * Atomically create a new single-device session for the given user,
     * invalidating and superseding any existing active session.
     *
     * Protected by database transaction and pessimistic row-level locking (FOR UPDATE)
     * to prevent race conditions during concurrent login attempts.
     */
    public function createDeviceSession(User $user, string $tokenName = 'api-token', ?DateTimeInterface $expiresAt = null): NewAccessToken
    {
        return DB::transaction(function () use ($user, $tokenName, $expiresAt) {
            /** @var User $lockedUser */
            $lockedUser = User::where('id', $user->id)->lockForUpdate()->firstOrFail();

            $previousTokenId = $lockedUser->current_device_token_id;

            // 1. If a previous active device session exists, record it as superseded and revoke it
            if ($previousTokenId) {
                // Register in the persistent superseded tokens table
                SupersededDeviceToken::create([
                    'user_id' => $lockedUser->id,
                    'token_id' => $previousTokenId,
                    'superseded_at' => now(),
                    'expires_at' => $expiresAt,
                ]);

                // Cache the superseded token ID for sub-millisecond lookup
                Cache::put('superseded_token:'.$previousTokenId, true, now()->addDays(7));

                // Revoke the old token record from personal_access_tokens
                $lockedUser->tokens()->where('id', $previousTokenId)->delete();
            }

            // Clean up any stale superseded records older than 30 days
            SupersededDeviceToken::where('user_id', $lockedUser->id)
                ->where('created_at', '<', now()->subDays(30))
                ->delete();

            // 2. Issue the new Sanctum personal access token
            $newToken = $lockedUser->createToken($tokenName, ['*'], $expiresAt);

            // 3. Persist the new active token ID on the user record
            $lockedUser->update([
                'current_device_token_id' => $newToken->accessToken->id,
            ]);

            // Sync the model instance in memory
            $user->current_device_token_id = $newToken->accessToken->id;

            return $newToken;
        });
    }

    /**
     * Invalidate the current device session on logout.
     */
    public function invalidateCurrentSession(User $user, ?int $tokenId = null): void
    {
        DB::transaction(function () use ($user, $tokenId) {
            /** @var User $lockedUser */
            $lockedUser = User::where('id', $user->id)->lockForUpdate()->first();
            if (! $lockedUser) {
                return;
            }

            $activeTokenId = $lockedUser->current_device_token_id;

            if ($tokenId) {
                Cache::forget('superseded_token:'.$tokenId);
                $lockedUser->tokens()->where('id', $tokenId)->delete();

                if ((int) $activeTokenId === $tokenId) {
                    $lockedUser->update(['current_device_token_id' => null]);
                    $user->current_device_token_id = null;
                }
            } else {
                if ($activeTokenId) {
                    Cache::forget('superseded_token:'.$activeTokenId);
                    $lockedUser->tokens()->where('id', $activeTokenId)->delete();
                    $lockedUser->update(['current_device_token_id' => null]);
                    $user->current_device_token_id = null;
                }
            }

            if (Auth::guard('sanctum')->check()) {
                Auth::guard('sanctum')->forgetUser();
            }
        });
    }

    /**
     * Revoke all active sessions and tokens for a user (e.g. On admin deactivation or password reset).
     */
    public function revokeAllSessions(User $user): void
    {
        DB::transaction(function () use ($user) {
            /** @var User $lockedUser */
            $lockedUser = User::where('id', $user->id)->lockForUpdate()->first();
            if (! $lockedUser) {
                return;
            }

            if ($lockedUser->current_device_token_id) {
                SupersededDeviceToken::create([
                    'user_id' => $lockedUser->id,
                    'token_id' => $lockedUser->current_device_token_id,
                    'superseded_at' => now(),
                ]);
                Cache::put('superseded_token:'.$lockedUser->current_device_token_id, true, now()->addDays(7));
            }

            $lockedUser->tokens()->delete();
            $lockedUser->update(['current_device_token_id' => null]);
            $user->current_device_token_id = null;

            if (Auth::guard('sanctum')->check()) {
                Auth::guard('sanctum')->forgetUser();
            }
        });
    }

    /**
     * Check if a token ID is the active session for the user.
     */
    public function isTokenActive(User $user, int $tokenId): bool
    {
        return (int) $user->current_device_token_id === $tokenId;
    }

    /**
     * Determine if a given token ID was superseded by another login.
     */
    public function isTokenSuperseded(int $tokenId): bool
    {
        $cached = Cache::get('superseded_token:'.$tokenId);
        if ($cached !== null) {
            return (bool) $cached;
        }

        $exists = SupersededDeviceToken::where('token_id', $tokenId)->exists();

        Cache::put('superseded_token:'.$tokenId, $exists, now()->addDays(7));

        return $exists;
    }
}
