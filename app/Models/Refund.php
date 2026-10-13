<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Refund extends Model
{
    use HasFactory;

    protected $fillable = [
        'payment_id',
        'user_id',
        'gateway_refund_id',
        'amount',
        'currency',
        'status',
        'reason',
        'notes',
        'initiated_by',
        'processed_at',
        'failed_at',
        'failure_reason',
        'metadata',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'integer',
            'notes' => 'array',
            'metadata' => 'array',
            'processed_at' => 'datetime',
            'failed_at' => 'datetime',
        ];
    }

    public function payment(): BelongsTo
    {
        return $this->belongsTo(Payment::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function initiator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'initiated_by');
    }

    public function getAmountRupeesAttribute(): float
    {
        return round($this->amount / 100, 2);
    }

    public function isProcessed(): bool
    {
        return $this->status === 'processed';
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isFailed(): bool
    {
        return $this->status === 'failed';
    }
}
