<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Chapter extends Model
{
    protected $fillable = [
        'subject_id',
        'class_level',
        'title',
        'image',
        'color',
        'theme',
        'sort_order',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'class_level' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Subject this chapter belongs to.
     */
    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    /**
     * Recorded classes in this chapter.
     */
    public function recordedClasses(): HasMany
    {
        return $this->hasMany(RecordedClass::class);
    }

    /**
     * Live classes in this chapter.
     */
    public function liveClasses(): HasMany
    {
        return $this->hasMany(LiveClass::class)->orderBy('scheduled_at');
    }

    /**
     * Notes in this chapter.
     */
    public function notes(): HasMany
    {
        return $this->hasMany(Note::class);
    }

    /**
     * Tests in this chapter.
     */
    public function tests(): HasMany
    {
        return $this->hasMany(Test::class);
    }

    /**
     * Progress records for this chapter.
     */
    public function progress(): HasMany
    {
        return $this->hasMany(Progress::class);
    }
}
