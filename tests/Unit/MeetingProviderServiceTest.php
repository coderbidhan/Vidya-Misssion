<?php

namespace Tests\Unit;

use App\Services\MeetingProviderService;
use PHPUnit\Framework\TestCase;

class MeetingProviderServiceTest extends TestCase
{
    /**
     * Test provider auto-detection from URLs.
     */
    public function test_it_detects_meeting_providers_correctly(): void
    {
        $this->assertEquals(
            MeetingProviderService::PROVIDER_GOOGLE_MEET,
            MeetingProviderService::detectProvider('https://meet.google.com/abc-defg-hij')
        );

        $this->assertEquals(
            MeetingProviderService::PROVIDER_ZOOM,
            MeetingProviderService::detectProvider('https://us05web.zoom.us/j/1234567890?pwd=test')
        );

        $this->assertEquals(
            MeetingProviderService::PROVIDER_TEAMS,
            MeetingProviderService::detectProvider('https://teams.microsoft.com/l/meetup-join/19%3ameeting')
        );

        $this->assertEquals(
            MeetingProviderService::PROVIDER_YOUTUBE_LIVE,
            MeetingProviderService::detectProvider('https://www.youtube.com/watch?v=dQw4w9WgXcQ')
        );

        $this->assertEquals(
            MeetingProviderService::PROVIDER_OTHER,
            MeetingProviderService::detectProvider('https://custom-conferencing.school.edu/room/42')
        );
    }

    /**
     * Test HTTPS protocol requirement.
     */
    public function test_it_rejects_non_https_urls(): void
    {
        $result = MeetingProviderService::validateMeetingUrl('http://meet.google.com/abc-defg-hij', MeetingProviderService::PROVIDER_GOOGLE_MEET);
        $this->assertFalse($result['valid']);
        $this->assertStringContainsString('HTTPS', $result['error']);
    }

    /**
     * Test Google Meet URL validation and meeting code extraction.
     */
    public function test_google_meet_validation_and_id_extraction(): void
    {
        $validUrl = 'https://meet.google.com/xyz-uvwx-rst';
        $validation = MeetingProviderService::validateMeetingUrl($validUrl, MeetingProviderService::PROVIDER_GOOGLE_MEET);
        $this->assertTrue($validation['valid']);

        $id = MeetingProviderService::extractMeetingId($validUrl, MeetingProviderService::PROVIDER_GOOGLE_MEET);
        $this->assertEquals('xyz-uvwx-rst', $id);

        $invalidUrl = 'https://other-domain.com/xyz-uvwx-rst';
        $invalidValidation = MeetingProviderService::validateMeetingUrl($invalidUrl, MeetingProviderService::PROVIDER_GOOGLE_MEET);
        $this->assertFalse($invalidValidation['valid']);
    }

    /**
     * Test Zoom URL validation and meeting ID extraction.
     */
    public function test_zoom_validation_and_id_extraction(): void
    {
        $zoomUrl = 'https://zoom.us/j/9876543210';
        $validation = MeetingProviderService::validateMeetingUrl($zoomUrl, MeetingProviderService::PROVIDER_ZOOM);
        $this->assertTrue($validation['valid']);

        $id = MeetingProviderService::extractMeetingId($zoomUrl, MeetingProviderService::PROVIDER_ZOOM);
        $this->assertEquals('9876543210', $id);
    }

    /**
     * Test URL masking for unauthorized users.
     */
    public function test_url_masking(): void
    {
        $masked = MeetingProviderService::maskMeetingUrl('https://meet.google.com/abc-defg-hij');
        $this->assertEquals('https://meet.google.com/***protected***', $masked);
    }
}
