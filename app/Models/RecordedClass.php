<?php

namespace App\Models;

use App\Services\YouTubeService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RecordedClass extends Model
{
    protected $fillable = [
        'chapter_id',
        'teacher_id',
        'title',
        'description',
        'video_url',
        'gdrive_file_id',
        'gdrive_embed_url',
        'thumbnail_url',
        'duration_seconds',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'duration_seconds' => 'integer',
        ];
    }

    protected static function booted(): void
    {
        static::saving(function (RecordedClass $model) {
            // When video_url is present, synchronize with YouTube metadata
            if (! empty($model->video_url)) {
                $ytId = YouTubeService::extractVideoId($model->video_url);
                if ($ytId) {
                    $model->video_url = "https://www.youtube.com/watch?v={$ytId}";
                    if (empty($model->gdrive_file_id)) {
                        $model->gdrive_file_id = $ytId;
                    }
                    if (empty($model->gdrive_embed_url) || str_contains($model->gdrive_embed_url, 'youtube.com')) {
                        $model->gdrive_embed_url = "https://www.youtube.com/embed/{$ytId}";
                    }
                    if (empty($model->thumbnail_url)) {
                        $model->thumbnail_url = YouTubeService::toThumbnailUrl($ytId);
                    }
                }
            } elseif (! empty($model->gdrive_file_id) && YouTubeService::isValidYouTube($model->gdrive_file_id)) {
                // If only legacy gdrive_file_id was passed and it's a YouTube ID
                $ytId = YouTubeService::extractVideoId($model->gdrive_file_id);
                $model->video_url = "https://www.youtube.com/watch?v={$ytId}";
                if (empty($model->gdrive_embed_url)) {
                    $model->gdrive_embed_url = "https://www.youtube.com/embed/{$ytId}";
                }
                if (empty($model->thumbnail_url)) {
                    $model->thumbnail_url = YouTubeService::toThumbnailUrl($ytId);
                }
            }
        });
    }

    /**
     * Accessor to always provide a valid video_url.
     */
    public function getVideoUrlAttribute(?string $value): ?string
    {
        if (! empty($value)) {
            return YouTubeService::toWatchUrl($value);
        }

        if (! empty($this->gdrive_embed_url) && str_contains($this->gdrive_embed_url, 'youtube.com')) {
            return YouTubeService::toWatchUrl($this->gdrive_embed_url);
        }

        if (! empty($this->gdrive_file_id) && YouTubeService::isValidYouTube($this->gdrive_file_id)) {
            return YouTubeService::toWatchUrl($this->gdrive_file_id);
        }

        return null;
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
