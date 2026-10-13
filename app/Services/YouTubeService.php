<?php

namespace App\Services;

class YouTubeService
{
    /**
     * Extract the 11-character YouTube video ID from various URL formats or raw ID.
     */
    public static function extractVideoId(?string $input): ?string
    {
        if (empty($input)) {
            return null;
        }

        $input = trim($input);

        // Direct 11-character video ID
        if (preg_match('/^[a-zA-Z0-9_-]{11}$/', $input)) {
            return $input;
        }

        // Standard YouTube URL formats (watch?v=, youtu.be/, /embed/, /v/, /shorts/)
        $pattern = '/(?:youtube\.com\/(?:watch\?(?:.*&)?v=|embed\/|v\/|shorts\/)|youtu\.be\/)([a-zA-Z0-9_-]{11})/i';
        if (preg_match($pattern, $input, $matches)) {
            return $matches[1];
        }

        return null;
    }

    /**
     * Normalize any YouTube URL or ID to a standard watch URL.
     */
    public static function toWatchUrl(?string $input): ?string
    {
        $id = self::extractVideoId($input);

        return $id ? "https://www.youtube.com/watch?v={$id}" : null;
    }

    /**
     * Generate an embed player URL for a YouTube video.
     */
    public static function toEmbedUrl(?string $input): ?string
    {
        $id = self::extractVideoId($input);

        return $id ? "https://www.youtube.com/embed/{$id}" : null;
    }

    /**
     * Generate a default high-quality thumbnail URL for a YouTube video.
     */
    public static function toThumbnailUrl(?string $input): ?string
    {
        $id = self::extractVideoId($input);

        return $id ? "https://img.youtube.com/vi/{$id}/hqdefault.jpg" : null;
    }

    /**
     * Validate whether an input string is a valid YouTube URL or video ID.
     */
    public static function isValidYouTube(?string $input): bool
    {
        return self::extractVideoId($input) !== null;
    }

    /**
     * Alias for isValidYouTube.
     */
    public static function isValidYouTubeUrl(?string $input): bool
    {
        return self::isValidYouTube($input);
    }
}
