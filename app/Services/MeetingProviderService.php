<?php

namespace App\Services;

class MeetingProviderService
{
    public const PROVIDER_GOOGLE_MEET = 'google_meet';

    public const PROVIDER_ZOOM = 'zoom';

    public const PROVIDER_TEAMS = 'teams';

    public const PROVIDER_YOUTUBE_LIVE = 'youtube_live';

    public const PROVIDER_OTHER = 'other';

    /**
     * Supported provider options with metadata.
     *
     * @return array<string, array{name: string, icon: string, example: string}>
     */
    public static function supportedProviders(): array
    {
        return [
            self::PROVIDER_GOOGLE_MEET => [
                'name' => 'Google Meet',
                'icon' => 'meet',
                'badge' => 'badge-success',
                'example' => 'https://meet.google.com/abc-defg-hij',
            ],
            self::PROVIDER_ZOOM => [
                'name' => 'Zoom Meetings',
                'icon' => 'zoom',
                'badge' => 'badge-info',
                'example' => 'https://us05web.zoom.us/j/1234567890',
            ],
            self::PROVIDER_TEAMS => [
                'name' => 'Microsoft Teams',
                'icon' => 'teams',
                'badge' => 'badge-purple',
                'example' => 'https://teams.microsoft.com/l/meetup-join/...',
            ],
            self::PROVIDER_YOUTUBE_LIVE => [
                'name' => 'YouTube Live Stream',
                'icon' => 'youtube',
                'badge' => 'badge-danger',
                'example' => 'https://www.youtube.com/watch?v=liveStreamId',
            ],
            self::PROVIDER_OTHER => [
                'name' => 'Other / Custom Provider',
                'icon' => 'video',
                'badge' => 'badge-neutral',
                'example' => 'https://conference.example.com/room-name',
            ],
        ];
    }

    /**
     * Detect provider from meeting URL string.
     */
    public static function detectProvider(?string $url): string
    {
        if (empty($url)) {
            return self::PROVIDER_OTHER;
        }

        $url = trim($url);

        if (str_contains($url, 'meet.google.com')) {
            return self::PROVIDER_GOOGLE_MEET;
        }

        if (str_contains($url, 'zoom.us')) {
            return self::PROVIDER_ZOOM;
        }

        if (str_contains($url, 'teams.microsoft.com') || str_contains($url, 'teams.live.com')) {
            return self::PROVIDER_TEAMS;
        }

        if (str_contains($url, 'youtube.com') || str_contains($url, 'youtu.be')) {
            return self::PROVIDER_YOUTUBE_LIVE;
        }

        return self::PROVIDER_OTHER;
    }

    /**
     * Validate whether a meeting URL is valid for the specified provider.
     *
     * @return array{valid: bool, error: ?string, detected_provider: string}
     */
    public static function validateMeetingUrl(string $url, ?string $provider = null): array
    {
        $url = trim($url);

        if (! filter_var($url, FILTER_VALIDATE_URL)) {
            return [
                'valid' => false,
                'error' => 'The meeting URL must be a well-formed URL.',
                'detected_provider' => self::PROVIDER_OTHER,
            ];
        }

        $scheme = parse_url($url, PHP_URL_SCHEME);
        if (strtolower((string) $scheme) !== 'https') {
            return [
                'valid' => false,
                'error' => 'The meeting URL must use a secure HTTPS protocol.',
                'detected_provider' => self::PROVIDER_OTHER,
            ];
        }

        $detected = self::detectProvider($url);
        $provider = $provider ?: $detected;

        switch ($provider) {
            case self::PROVIDER_GOOGLE_MEET:
                if (! str_contains($url, 'meet.google.com')) {
                    return [
                        'valid' => false,
                        'error' => 'Google Meet URLs must be hosted on meet.google.com (e.g. https://meet.google.com/abc-defg-hij).',
                        'detected_provider' => $detected,
                    ];
                }
                break;

            case self::PROVIDER_ZOOM:
                if (! str_contains($url, 'zoom.us')) {
                    return [
                        'valid' => false,
                        'error' => 'Zoom meeting URLs must be hosted on zoom.us (e.g. https://zoom.us/j/1234567890).',
                        'detected_provider' => $detected,
                    ];
                }
                break;

            case self::PROVIDER_TEAMS:
                if (! str_contains($url, 'teams.microsoft.com') && ! str_contains($url, 'teams.live.com')) {
                    return [
                        'valid' => false,
                        'error' => 'Microsoft Teams URLs must be on teams.microsoft.com or teams.live.com.',
                        'detected_provider' => $detected,
                    ];
                }
                break;

            case self::PROVIDER_YOUTUBE_LIVE:
                if (! YouTubeService::isValidYouTube($url)) {
                    return [
                        'valid' => false,
                        'error' => 'YouTube Live URLs must be a valid YouTube watch or stream link.',
                        'detected_provider' => $detected,
                    ];
                }
                break;

            case self::PROVIDER_OTHER:
            default:
                // Any valid HTTPS URL is accepted
                break;
        }

        return [
            'valid' => true,
            'error' => null,
            'detected_provider' => $detected,
        ];
    }

    /**
     * Extract meeting code or identifier if not provided explicitly.
     */
    public static function extractMeetingId(?string $url, string $platform): ?string
    {
        if (empty($url)) {
            return null;
        }

        $url = trim($url);

        if ($platform === self::PROVIDER_GOOGLE_MEET) {
            // meet.google.com/abc-defg-hij
            if (preg_match('/meet\.google\.com\/([a-z]{3}-[a-z]{4}-[a-z]{3})/i', $url, $m)) {
                return strtolower($m[1]);
            }
        }

        if ($platform === self::PROVIDER_ZOOM) {
            // zoom.us/j/1234567890 or zoom.us/my/username
            if (preg_match('/zoom\.us\/(?:j|my)\/([a-zA-Z0-9_-]+)/i', $url, $m)) {
                return $m[1];
            }
        }

        if ($platform === self::PROVIDER_YOUTUBE_LIVE) {
            return YouTubeService::extractVideoId($url);
        }

        return null;
    }

    /**
     * Mask meeting URL for unauthorized or preview contexts.
     * E.g. https://meet.google.com/***lock***
     */
    public static function maskMeetingUrl(string $url): string
    {
        $parsed = parse_url($url);
        $host = $parsed['host'] ?? 'meeting';

        return "https://{$host}/***protected***";
    }
}
