<?php

namespace Tests\Unit;

use App\Services\YouTubeService;
use PHPUnit\Framework\TestCase;

class YouTubeServiceTest extends TestCase
{
    public function test_extracts_video_id_from_standard_watch_url(): void
    {
        $url = 'https://www.youtube.com/watch?v=dQw4w9WgXcQ';
        $this->assertEquals('dQw4w9WgXcQ', YouTubeService::extractVideoId($url));
    }

    public function test_extracts_video_id_from_short_url(): void
    {
        $url = 'https://youtu.be/dQw4w9WgXcQ';
        $this->assertEquals('dQw4w9WgXcQ', YouTubeService::extractVideoId($url));
    }

    public function test_extracts_video_id_from_embed_url(): void
    {
        $url = 'https://www.youtube.com/embed/dQw4w9WgXcQ';
        $this->assertEquals('dQw4w9WgXcQ', YouTubeService::extractVideoId($url));
    }

    public function test_extracts_video_id_from_url_with_additional_query_parameters(): void
    {
        $url = 'https://www.youtube.com/watch?v=dQw4w9WgXcQ&feature=share&t=120';
        $this->assertEquals('dQw4w9WgXcQ', YouTubeService::extractVideoId($url));
    }

    public function test_extracts_video_id_from_mobile_url(): void
    {
        $url = 'https://m.youtube.com/watch?v=dQw4w9WgXcQ';
        $this->assertEquals('dQw4w9WgXcQ', YouTubeService::extractVideoId($url));
    }

    public function test_accepts_raw_11_character_video_id(): void
    {
        $this->assertEquals('dQw4w9WgXcQ', YouTubeService::extractVideoId('dQw4w9WgXcQ'));
    }

    public function test_returns_null_for_non_youtube_urls(): void
    {
        $this->assertNull(YouTubeService::extractVideoId('https://vimeo.com/12345678'));
        $this->assertNull(YouTubeService::extractVideoId('https://dailymotion.com/video/x7tgad0'));
        $this->assertNull(YouTubeService::extractVideoId('https://example.com/video.mp4'));
    }

    public function test_returns_null_for_invalid_or_malformed_input(): void
    {
        $this->assertNull(YouTubeService::extractVideoId(null));
        $this->assertNull(YouTubeService::extractVideoId(''));
        $this->assertNull(YouTubeService::extractVideoId('   '));
        $this->assertNull(YouTubeService::extractVideoId('not-a-valid-url'));
        $this->assertNull(YouTubeService::extractVideoId('<script>alert("xss")</script>'));
    }

    public function test_converts_to_normalized_watch_url(): void
    {
        $expected = 'https://www.youtube.com/watch?v=dQw4w9WgXcQ';
        $this->assertEquals($expected, YouTubeService::toWatchUrl('https://youtu.be/dQw4w9WgXcQ'));
        $this->assertEquals($expected, YouTubeService::toWatchUrl('dQw4w9WgXcQ'));
        $this->assertEquals($expected, YouTubeService::toWatchUrl('https://www.youtube.com/embed/dQw4w9WgXcQ'));
        $this->assertNull(YouTubeService::toWatchUrl('not-a-valid-youtube-link'));
    }

    public function test_converts_to_embed_url(): void
    {
        $expected = 'https://www.youtube.com/embed/dQw4w9WgXcQ';
        $this->assertEquals($expected, YouTubeService::toEmbedUrl('https://www.youtube.com/watch?v=dQw4w9WgXcQ'));
        $this->assertEquals($expected, YouTubeService::toEmbedUrl('dQw4w9WgXcQ'));
        $this->assertNull(YouTubeService::toEmbedUrl('not-a-valid-youtube-link'));
    }

    public function test_validates_youtube_urls(): void
    {
        $this->assertTrue(YouTubeService::isValidYouTubeUrl('https://www.youtube.com/watch?v=dQw4w9WgXcQ'));
        $this->assertTrue(YouTubeService::isValidYouTubeUrl('https://youtu.be/dQw4w9WgXcQ'));
        $this->assertTrue(YouTubeService::isValidYouTubeUrl('https://www.youtube.com/embed/dQw4w9WgXcQ'));
        $this->assertTrue(YouTubeService::isValidYouTubeUrl('dQw4w9WgXcQ'));

        $this->assertFalse(YouTubeService::isValidYouTubeUrl('https://vimeo.com/123456'));
        $this->assertFalse(YouTubeService::isValidYouTubeUrl(''));
        $this->assertFalse(YouTubeService::isValidYouTubeUrl(null));
        $this->assertFalse(YouTubeService::isValidYouTubeUrl('random string'));
    }
}
