<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Test extends Model
{
    protected $fillable = [
        'chapter_id',
        'title',
        'total_marks',
        'pass_marks',
        'duration_minutes',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'total_marks' => 'integer',
            'pass_marks' => 'integer',
            'duration_minutes' => 'integer',
        ];
    }

    /**
     * The chapter this test belongs to.
     */
    public function chapter(): BelongsTo
    {
        return $this->belongsTo(Chapter::class);
    }

    /**
     * All questions in this test.
     */
    public function questions(): HasMany
    {
        return $this->hasMany(Question::class)->orderBy('sort_order');
    }

    /**
     * All attempts on this test.
     */
    public function attempts(): HasMany
    {
        return $this->hasMany(TestAttempt::class);
    }
}
