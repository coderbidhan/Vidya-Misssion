<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RecordedClass extends Model
{
    protected $fillable = [
        'chapter_id',
        'teacher_id',
        'title',
        'description',
        'gdrive_file_id',
        'gdrive_embed_url',
        'thumbnail_url',
        'duration_seconds',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active'        => 'boolean',
            'duration_seconds' => 'integer',
        ];
    }

    /**
     * The chapter this class belongs to.
     */
    public function chapter(): BelongsTo
    {
        return $this->belongsTo(Chapter::class);
    }

    /**
     * The teacher who created this class.
     */
    public function teacher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }
}
