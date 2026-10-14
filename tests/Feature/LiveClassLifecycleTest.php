<?php

namespace Tests\Feature;

use App\Models\Chapter;
use App\Models\ClassSubject;
use App\Models\LiveClass;
use App\Models\Notification;
use App\Models\RecordedClass;
use App\Models\StudentProfile;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class LiveClassLifecycleTest extends TestCase
{
    use RefreshDatabase;

    protected User $teacher;

    protected User $student;

    protected Subject $subject;

    protected Chapter $chapter;

    protected function setUp(): void
    {
        parent::setUp();

        $this->teacher = User::create([
            'name' => 'Faculty Teacher',
            'email' => 'teacher@test.com',
            'mobile' => '8888880003',
            'role' => 'teacher',
            'password' => bcrypt('password'),
            'is_active' => true,
            'is_verified' => true,
        ]);

        $this->student = User::create([
            'name' => 'Grade 10 Student',
            'email' => 'student10@test.com',
            'mobile' => '7777770004',
            'role' => 'student',
            'password' => bcrypt('password'),
            'is_active' => true,
            'is_verified' => true,
        ]);
        StudentProfile::create([
            'user_id' => $this->student->id,
            'name' => 'Grade 10 Student',
            'class' => 'Class 10',
            'school_name' => 'Siliguri Boys High School',
            'district' => 'Darjeeling',
        ]);

        $this->subject = Subject::create([
            'name' => 'Life Science',
            'color' => '#10B981',
            'icon' => 'leaf',
            'sort_order' => 1,
            'is_active' => true,
        ]);

        ClassSubject::create([
            'class_level' => 10,
            'subject_id' => $this->subject->id,
        ]);

        $this->chapter = Chapter::create([
            'subject_id' => $this->subject->id,
            'class_level' => 10,
            'title' => 'Cell Division & Genetics',
            'sort_order' => 1,
            'is_active' => true,
        ]);
    }

    /**
     * Test lifecycle command auto-completes live sessions whose scheduled window has elapsed.
     */
    public function test_lifecycle_command_auto_completes_past_live_sessions(): void
    {
        // Session that started 90 minutes ago, lasted 60 minutes -> ended 30 minutes ago
        $pastLive = LiveClass::create([
            'class_level' => 10,
            'subject_id' => $this->subject->id,
            'teacher_id' => $this->teacher->id,
            'title' => 'Past Live Genetics Class',
            'platform' => 'google_meet',
            'meeting_url' => 'https://meet.google.com/abc-defg-hij',
            'scheduled_at' => now()->subMinutes(90),
            'duration_minutes' => 60,
            'status' => 'live',
            'is_published' => true,
        ]);

        $this->assertEquals('live', $pastLive->fresh()->status);

        Artisan::call('live-classes:lifecycle');

        $this->assertEquals('completed', $pastLive->fresh()->status);
    }

    /**
     * Test sessions requiring attention are identified correctly.
     */
    public function test_sessions_requiring_attention_are_flagged(): void
    {
        // 1. Session missing meeting link
        $noLink = LiveClass::create([
            'class_level' => 10,
            'subject_id' => $this->subject->id,
            'teacher_id' => $this->teacher->id,
            'title' => 'Missing Link Session',
            'platform' => 'other',
            'meeting_url' => '',
            'scheduled_at' => now()->addDays(1),
            'duration_minutes' => 60,
            'status' => 'scheduled',
        ]);

        // 2. Session past scheduled start time by 45 minutes but still 'scheduled'
        $pastScheduled = LiveClass::create([
            'class_level' => 10,
            'subject_id' => $this->subject->id,
            'teacher_id' => $this->teacher->id,
            'title' => 'Past Due Unstarted Session',
            'platform' => 'google_meet',
            'meeting_url' => 'https://meet.google.com/xyz-uvwx-rst',
            'scheduled_at' => now()->subMinutes(45),
            'duration_minutes' => 60,
            'status' => 'scheduled',
        ]);

        $attentionClasses = LiveClass::requiringAttention()->pluck('id')->toArray();

        $this->assertContains($noLink->id, $attentionClasses);
        $this->assertContains($pastScheduled->id, $attentionClasses);
    }

    /**
     * Test idempotent dispatch of upcoming class student notifications.
     */
    public function test_upcoming_reminders_are_dispatched_idempotently(): void
    {
        // Session starting in 15 minutes
        $startingSoon = LiveClass::create([
            'class_level' => 10,
            'subject_id' => $this->subject->id,
            'teacher_id' => $this->teacher->id,
            'title' => 'Imminent Live Biology Revision',
            'platform' => 'google_meet',
            'meeting_url' => 'https://meet.google.com/bio-live-session',
            'scheduled_at' => now()->addMinutes(15),
            'duration_minutes' => 60,
            'status' => 'scheduled',
            'is_published' => true,
        ]);

        // Run command first time
        Artisan::call('live-classes:lifecycle');

        $notifications = Notification::where('user_id', $this->student->id)
            ->where('type', 'live_class_reminder')
            ->get();

        $this->assertCount(1, $notifications);
        $this->assertStringContainsString('Imminent Live Biology Revision', $notifications->first()->title);

        // Run command second time - must be idempotent and not create duplicate notifications
        Artisan::call('live-classes:lifecycle');

        $notificationsAfterSecondRun = Notification::where('user_id', $this->student->id)
            ->where('type', 'live_class_reminder')
            ->get();

        $this->assertCount(1, $notificationsAfterSecondRun);
    }

    /**
     * CRITICAL REGRESSION CHECK: RecordedClass video_url behavior remains 100% intact.
     */
    public function test_recorded_class_video_url_contract_remains_unaffected(): void
    {
        $rec = RecordedClass::create([
            'chapter_id' => $this->chapter->id,
            'teacher_id' => $this->teacher->id,
            'title' => 'Recorded Cell Division Lecture',
            'video_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
            'duration_seconds' => 1800,
            'is_active' => true,
        ]);

        $this->assertEquals('https://www.youtube.com/watch?v=dQw4w9WgXcQ', $rec->video_url);
        $this->assertEquals('https://www.youtube.com/embed/dQw4w9WgXcQ', $rec->gdrive_embed_url);
        $this->assertEquals('https://img.youtube.com/vi/dQw4w9WgXcQ/hqdefault.jpg', $rec->thumbnail_url);
    }
}
