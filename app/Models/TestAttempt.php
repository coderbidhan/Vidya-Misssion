<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TestAttempt extends Model
{
    protected $fillable = [
        'test_id',
        'user_id',
        'score',
        'total_marks',
        'is_passed',
        'answers',
        'started_at',
        'completed_at',
    ];

    protected function casts(): array
    {
        return [
            'is_passed'    => 'boolean',
            'answers'      => 'array',
            'started_at'   => 'datetime',
            'completed_at' => 'datetime',
        ];
    }

    /**
     * The test this attempt is for.
     */
    public function test(): BelongsTo
    {
        return $this->belongsTo(Test::class);
    }

    /**
     * The user who made this attempt.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
