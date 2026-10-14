<?php

namespace App\Models;

use App\Services\MeetingProviderService;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LiveClass extends Model
{
    protected $fillable = [
        'class_level',
        'subject_id',
        'chapter_id',
        'teacher_id',
        'title',
        'description',
        'learning_objectives',
        'platform',
        'meeting_url',
        'meeting_id',
        'scheduled_at',
        'ends_at',
        'duration_minutes',
        'timezone',
        'status',
        'is_published',
        'recording_url',
        'cancellation_reason',
        'created_by',
        'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'class_level' => 'integer',
            'scheduled_at' => 'datetime',
            'ends_at' => 'datetime',
            'duration_minutes' => 'integer',
            'is_published' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        static::saving(function (LiveClass $model) {
            // Auto-sync class_level and subject_id from Chapter if associated
            if ($model->chapter_id && (! $model->class_level || ! $model->subject_id)) {
                $chapter = Chapter::find($model->chapter_id);
                if ($chapter) {
                    $model->class_level = $model->class_level ?: $chapter->class_level;
                    $model->subject_id = $model->subject_id ?: $chapter->subject_id;
                }
            }

            // Automatically compute ends_at if scheduled_at and duration_minutes are set
            if ($model->scheduled_at && $model->duration_minutes) {
                $start = Carbon::parse($model->scheduled_at);
                $model->ends_at = $start->copy()->addMinutes($model->duration_minutes);
            }

            // Auto-extract meeting ID if empty and URL is present
            if (empty($model->meeting_id) && ! empty($model->meeting_url)) {
                $model->meeting_id = MeetingProviderService::extractMeetingId($model->meeting_url, $model->platform ?? 'other');
            }

            // Default timezone
            if (empty($model->timezone)) {
                $model->timezone = 'Asia/Kolkata';
            }
        });
    }

    // -------------------------------------------------------------------------
    // Relationships
    // -------------------------------------------------------------------------

    /**
     * The subject this live class belongs to.
     */
    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    /**
     * The chapter this live class belongs to (optional).
     */
    public function chapter(): BelongsTo
    {
        return $this->belongsTo(Chapter::class);
    }

    /**
     * The teacher hosting this live class.
     */
    public function teacher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    /**
     * The administrator or user who created this live class.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * The administrator or user who last updated this live class.
     */
    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    // -------------------------------------------------------------------------
    // Query Scopes
    // -------------------------------------------------------------------------

    public function scopeForClass(Builder $query, int $level): Builder
    {
        return $query->where('class_level', $level);
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('is_published', true);
    }

    public function scopeUpcoming(Builder $query): Builder
    {
        return $query->where('status', 'scheduled')
            ->where('scheduled_at', '>=', now())
            ->orderBy('scheduled_at', 'asc');
    }

    public function scopeLive(Builder $query): Builder
    {
        return $query->where('status', 'live');
    }

    public function scopeCompleted(Builder $query): Builder
    {
        return $query->where('status', 'completed');
    }

    public function scopeCancelled(Builder $query): Builder
    {
        return $query->where('status', 'cancelled');
    }

    /**
     * Sessions requiring attention:
     * - Missing or invalid meeting URL
     * - Scheduled start time has passed (by >15 mins) but status is still 'scheduled'
     */
    public function scopeRequiringAttention(Builder $query): Builder
    {
        return $query->where(function ($q) {
            $q->whereNull('meeting_url')
                ->orWhere('meeting_url', '')
                ->orWhere(function ($pastScheduled) {
                    $pastScheduled->where('status', 'scheduled')
                        ->where('scheduled_at', '<', now()->subMinutes(15));
                });
        });
    }

    // -------------------------------------------------------------------------
    // Lifecycle & State Helpers
    // -------------------------------------------------------------------------

    public function isLive(): bool
    {
        return $this->status === 'live';
    }

    public function isUpcoming(): bool
    {
        return $this->status === 'scheduled' && $this->scheduled_at && $this->scheduled_at->isFuture();
    }

    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    public function isCancelled(): bool
    {
        return $this->status === 'cancelled';
    }

    public function canGoLive(): bool
    {
        return in_array($this->status, ['scheduled', 'draft']);
    }

    public function canComplete(): bool
    {
        return in_array($this->status, ['live', 'scheduled']);
    }

    public function canReschedule(): bool
    {
        return in_array($this->status, ['scheduled', 'cancelled']);
    }

    public function canCancel(): bool
    {
        return in_array($this->status, ['scheduled', 'live']);
    }

    public function hasEnded(): bool
    {
        if ($this->ends_at) {
            return now()->greaterThan($this->ends_at);
        }

        if ($this->scheduled_at) {
            return now()->greaterThan($this->scheduled_at->copy()->addMinutes($this->duration_minutes ?? 60));
        }

        return false;
    }
}
