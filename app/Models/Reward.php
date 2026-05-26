<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Reward extends Model
{
    protected $fillable = [
        'user_id',
        'chapter_id',
        'reward_type',
        'reward_data',
        'earned_at',
    ];

    protected function casts(): array
    {
        return [
            'reward_data' => 'array',
            'earned_at'   => 'datetime',
        ];
    }

    /**
     * The user who earned this reward.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * The chapter associated with this reward.
     */
    public function chapter(): BelongsTo
    {
        return $this->belongsTo(Chapter::class);
    }
}
