<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Str;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'uuid',
        'user_id',
        'payable_type',
        'payable_id',
        'purpose',
        'gateway',
        'gateway_order_id',
        'gateway_payment_id',
        'gateway_signature',
        'amount',
        'currency',
        'status',
        'gateway_status',
        'payment_method',
        'receipt',
        'is_captured',
        'captured_at',
        'authorized_at',
        'failed_at',
        'failure_code',
        'failure_reason',
        'amount_refunded',
        'metadata',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'integer',
            'amount_refunded' => 'integer',
            'is_captured' => 'boolean',
            'captured_at' => 'datetime',
            'authorized_at' => 'datetime',
            'failed_at' => 'datetime',
            'metadata' => 'array',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Payment $payment) {
            if (empty($payment->uuid)) {
                $payment->uuid = (string) Str::uuid();
            }
        });
    }

    // -------------------------------------------------
    // Relationships
    // -------------------------------------------------

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function payable(): MorphTo
    {
        return $this->morphTo();
    }

    public function userSubscription(): HasOne
    {
        return $this->hasOne(UserSubscription::class, 'payment_record_id');
    }

    public function refunds(): HasMany
    {
        return $this->hasMany(Refund::class);
    }

    // -------------------------------------------------
    // Helpers & Accessors
    // -------------------------------------------------

    public function getAmountRupeesAttribute(): float
    {
        return round($this->amount / 100, 2);
    }

    public function getRefundedRupeesAttribute(): float
    {
        return round($this->amount_refunded / 100, 2);
    }

    public function getRefundableBalanceAttribute(): int
    {
        return max(0, $this->amount - $this->amount_refunded);
    }

    public function getRefundableBalanceRupeesAttribute(): float
    {
        return round($this->refundable_balance / 100, 2);
    }

    public function isCaptured(): bool
    {
        return $this->is_captured || $this->status === 'captured';
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isFailed(): bool
    {
        return $this->status === 'failed';
    }

    public function isRefunded(): bool
    {
        return $this->status === 'refunded';
    }

    public function isPartiallyRefunded(): bool
    {
        return $this->status === 'partially_refunded';
    }

    public function canBeRefunded(): bool
    {
        return $this->isCaptured() && $this->refundable_balance > 0;
    }
}
