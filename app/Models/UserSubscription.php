<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserSubscription extends Model
{
    protected $fillable = [
        'user_id',
        'subscription_id',
        'payment_id',
        'payment_gateway',
        'payment_status',
        'starts_at',
        'ends_at',
    ];

    protected function casts(): array
    {
        return [
            'starts_at' => 'datetime',
            'ends_at' => 'datetime',
        ];
    }

    /**
     * The user who purchased this subscription.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * The subscription plan purchased.
     */
    public function subscription(): BelongsTo
    {
        return $this->belongsTo(Subscription::class);
    }

    /**
     * Determine if this subscription is currently active.
     */
    public function isActive(): bool
    {
        return $this->payment_status === 'completed'
            && ($this->starts_at === null || $this->starts_at->lessThanOrEqualTo(now()))
            && $this->ends_at !== null
            && $this->ends_at->greaterThanOrEqualTo(now());
    }

    /**
     * Get remaining days in the subscription period.
     */
    public function daysRemaining(): int
    {
        if (! $this->isActive() || ! $this->ends_at) {
            return 0;
        }

        return max(0, (int) now()->diffInDays($this->ends_at, false));
    }
}
