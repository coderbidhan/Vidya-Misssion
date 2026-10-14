<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AcademicClass extends Model
{
    use HasFactory;

    protected $table = 'academic_classes';

    protected $fillable = [
        'class_level',
        'name',
        'display_name',
        'board',
        'curriculum',
        'stage',
        'stream',
        'icon',
        'description',
        'sort_order',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'class_level' => 'integer',
            'sort_order' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Scope a query to only include active classes.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to order classes logically.
     */
    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort_order')->orderBy('class_level');
    }

    /**
     * Class-to-Subject mappings for this class level.
     */
    public function classSubjects(): HasMany
    {
        return $this->hasMany(ClassSubject::class, 'class_level', 'class_level');
    }

    /**
     * Chapters assigned directly to this class level.
     */
    public function chapters(): HasMany
    {
        return $this->hasMany(Chapter::class, 'class_level', 'class_level');
    }

    /**
     * Student profiles belonging to this class level.
     */
    public function studentProfiles(): HasMany
    {
        return $this->hasMany(StudentProfile::class, 'class', 'class_level');
    }

    /**
     * Live classes scheduled for this class level.
     */
    public function liveClasses(): HasMany
    {
        return $this->hasMany(LiveClass::class, 'class_level', 'class_level');
    }
}
