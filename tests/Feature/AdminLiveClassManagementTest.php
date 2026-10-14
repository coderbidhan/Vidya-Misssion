<?php

namespace Tests\Feature;

use App\Models\Chapter;
use App\Models\ClassSubject;
use App\Models\LiveClass;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class AdminLiveClassManagementTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected User $teacher;

    protected User $student;

    protected Subject $subject;

    protected Chapter $chapter;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::create([
            'name' => 'Admin Controller',
            'email' => 'admin@test.com',
            'mobile' => '9999990001',
            'role' => 'admin',
            'password' => bcrypt('password'),
            'is_active' => true,
            'is_verified' => true,
        ]);

        $this->teacher = User::create([
            'name' => 'Faculty Teacher',
            'email' => 'teacher@test.com',
            'mobile' => '8888880001',
            'role' => 'teacher',
            'password' => bcrypt('password'),
            'is_active' => true,
            'is_verified' => true,
        ]);

        $this->student = User::create([
            'name' => 'Enrolled Student',
            'email' => 'student@test.com',
            'mobile' => '7777770001',
            'role' => 'student',
            'password' => bcrypt('password'),
            'is_active' => true,
            'is_verified' => true,
        ]);

        $this->subject = Subject::create([
            'name' => 'Mathematics',
            'color' => '#3B82F6',
            'icon' => 'calculator',
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
            'title' => 'Quadratic Equations',
            'sort_order' => 1,
            'is_active' => true,
        ]);
    }

    /**
     * Non-admin users cannot access admin live class endpoints.
     */
    public function test_non_admin_users_cannot_access_admin_live_classes(): void
    {
        Sanctum::actingAs($this->student);

        $response = $this->getJson('/api/admin/live-classes');
        $response->assertStatus(403);

        $createResponse = $this->postJson('/api/admin/live-classes', []);
        $createResponse->assertStatus(403);
    }

    /**
     * Dashboard stats returns real operational counts.
     */
    public function test_dashboard_stats_endpoint_returns_real_metrics(): void
    {
        LiveClass::create([
            'class_level' => 10,
            'subject_id' => $this->subject->id,
            'teacher_id' => $this->teacher->id,
            'title' => 'Class 10 Live Prep',
            'platform' => 'google_meet',
            'meeting_url' => 'https://meet.google.com/abc-defg-hij',
            'scheduled_at' => now()->addDays(2),
            'duration_minutes' => 60,
            'status' => 'scheduled',
            'is_published' => true,
        ]);

        LiveClass::create([
            'class_level' => 9,
            'subject_id' => $this->subject->id,
            'teacher_id' => $this->teacher->id,
            'title' => 'Class 9 Live Algebra',
            'platform' => 'zoom',
            'meeting_url' => 'https://zoom.us/j/1234567890',
            'scheduled_at' => now()->addHour(),
            'duration_minutes' => 45,
            'status' => 'live',
            'is_published' => true,
        ]);

        Sanctum::actingAs($this->admin);

        $response = $this->getJson('/api/admin/live-classes/dashboard-stats');

        $response->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.total_classes', 2)
            ->assertJsonPath('data.scheduled_classes', 1)
            ->assertJsonPath('data.live_classes', 1)
            ->assertJsonPath('data.completed_classes', 0)
            ->assertJsonPath('data.grade_breakdown.10', 1)
            ->assertJsonPath('data.grade_breakdown.9', 1);
    }

    /**
     * Admin can schedule a new live class for Class 1–10.
     */
    public function test_admin_can_schedule_live_class(): void
    {
        Sanctum::actingAs($this->admin);

        $scheduledAt = now()->addDays(1)->setHour(11)->setMinute(0)->setSecond(0);

        $response = $this->postJson('/api/admin/live-classes', [
            'class_level' => 10,
            'subject_id' => $this->subject->id,
            'chapter_id' => $this->chapter->id,
            'teacher_id' => $this->teacher->id,
            'title' => 'Madhyamik Math Live Revision',
            'description' => 'Comprehensive question review for Class 10.',
            'learning_objectives' => 'Solve 5 previous year question papers.',
            'platform' => 'google_meet',
            'meeting_url' => 'https://meet.google.com/abc-defg-hij',
            'scheduled_at' => $scheduledAt->toDateTimeString(),
            'duration_minutes' => 90,
            'timezone' => 'Asia/Kolkata',
            'is_published' => true,
            'notify_students' => true,
        ]);

        $response->assertStatus(201)
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.title', 'Madhyamik Math Live Revision')
            ->assertJsonPath('data.class_level', 10)
            ->assertJsonPath('data.status', 'scheduled')
            ->assertJsonPath('data.meeting_id', 'abc-defg-hij');

        $this->assertDatabaseHas('live_classes', [
            'title' => 'Madhyamik Math Live Revision',
            'class_level' => 10,
            'duration_minutes' => 90,
        ]);

        // Verify ends_at was automatically computed to scheduled_at + 90 minutes
        $class = LiveClass::where('title', 'Madhyamik Math Live Revision')->first();
        $this->assertNotNull($class->ends_at);
        $this->assertEquals(
            $scheduledAt->copy()->addMinutes(90)->toDateTimeString(),
            $class->ends_at->toDateTimeString()
        );
    }

    /**
     * Schedule overlap for same teacher is prevented.
     */
    public function test_teacher_schedule_overlap_is_rejected(): void
    {
        Sanctum::actingAs($this->admin);

        $start = now()->addDays(2)->setHour(14)->setMinute(0)->setSecond(0);

        LiveClass::create([
            'class_level' => 10,
            'subject_id' => $this->subject->id,
            'teacher_id' => $this->teacher->id,
            'title' => 'Class 10 Session 1',
            'platform' => 'google_meet',
            'meeting_url' => 'https://meet.google.com/abc-defg-hij',
            'scheduled_at' => $start,
            'duration_minutes' => 60,
            'status' => 'scheduled',
        ]);

        // Attempt to book another class for same teacher at overlapping time (14:30)
        $overlapResponse = $this->postJson('/api/admin/live-classes', [
            'class_level' => 9,
            'subject_id' => $this->subject->id,
            'teacher_id' => $this->teacher->id,
            'title' => 'Class 9 Conflicting Session',
            'platform' => 'google_meet',
            'meeting_url' => 'https://meet.google.com/xyz-uvwx-rst',
            'scheduled_at' => $start->copy()->addMinutes(30)->toDateTimeString(),
            'duration_minutes' => 60,
        ]);

        $overlapResponse->assertStatus(422)
            ->assertJsonValidationErrors(['teacher_id']);
    }

    /**
     * Meeting URL format validation enforces HTTPS and provider rules.
     */
    public function test_invalid_meeting_urls_are_rejected(): void
    {
        Sanctum::actingAs($this->admin);

        // Non-HTTPS URL
        $response = $this->postJson('/api/admin/live-classes', [
            'class_level' => 10,
            'subject_id' => $this->subject->id,
            'teacher_id' => $this->teacher->id,
            'title' => 'Insecure Meeting Test',
            'platform' => 'google_meet',
            'meeting_url' => 'http://meet.google.com/abc-defg-hij',
            'scheduled_at' => now()->addDay()->toDateTimeString(),
            'duration_minutes' => 60,
        ]);
        $response->assertStatus(422)->assertJsonValidationErrors(['meeting_url']);

        // Google Meet selected, but domain is wrong
        $response2 = $this->postJson('/api/admin/live-classes', [
            'class_level' => 10,
            'subject_id' => $this->subject->id,
            'teacher_id' => $this->teacher->id,
            'title' => 'Mismatched Provider Test',
            'platform' => 'google_meet',
            'meeting_url' => 'https://zoom.us/j/1234567890',
            'scheduled_at' => now()->addDay()->toDateTimeString(),
            'duration_minutes' => 60,
        ]);
        $response2->assertStatus(422)->assertJsonValidationErrors(['meeting_url']);
    }

    /**
     * Admin can filter live classes by class level, status, and search.
     */
    public function test_admin_can_filter_live_classes(): void
    {
        Sanctum::actingAs($this->admin);

        LiveClass::create([
            'class_level' => 10,
            'subject_id' => $this->subject->id,
            'teacher_id' => $this->teacher->id,
            'title' => 'Grade 10 Special Trigonometry',
            'platform' => 'google_meet',
            'meeting_url' => 'https://meet.google.com/abc-defg-hij',
            'scheduled_at' => now()->addDays(3),
            'duration_minutes' => 60,
            'status' => 'scheduled',
        ]);

        LiveClass::create([
            'class_level' => 5,
            'subject_id' => $this->subject->id,
            'teacher_id' => $this->teacher->id,
            'title' => 'Grade 5 Primary Arithmetic',
            'platform' => 'google_meet',
            'meeting_url' => 'https://meet.google.com/def-ghij-klm',
            'scheduled_at' => now()->addDays(4),
            'duration_minutes' => 45,
            'status' => 'scheduled',
        ]);

        // Filter by class_level=10
        $resClass10 = $this->getJson('/api/admin/live-classes?class_level=10');
        $resClass10->assertStatus(200);
        $this->assertEquals(1, $resClass10->json('meta.total'));
        $this->assertEquals('Grade 10 Special Trigonometry', $resClass10->json('data.0.title'));

        // Search by keyword "Arithmetic"
        $resSearch = $this->getJson('/api/admin/live-classes?search=Arithmetic');
        $resSearch->assertStatus(200);
        $this->assertEquals(1, $resSearch->json('meta.total'));
        $this->assertEquals('Grade 5 Primary Arithmetic', $resSearch->json('data.0.title'));
    }

    /**
     * Admin can reschedule, cancel, and complete live class.
     */
    public function test_admin_lifecycle_transitions_reschedule_cancel_complete(): void
    {
        Sanctum::actingAs($this->admin);

        $liveClass = LiveClass::create([
            'class_level' => 10,
            'subject_id' => $this->subject->id,
            'teacher_id' => $this->teacher->id,
            'title' => 'Lifecycle Test Session',
            'platform' => 'google_meet',
            'meeting_url' => 'https://meet.google.com/abc-defg-hij',
            'scheduled_at' => now()->addDays(1),
            'duration_minutes' => 60,
            'status' => 'scheduled',
            'is_published' => true,
        ]);

        // 1. Reschedule
        $newTime = now()->addDays(3)->setHour(16)->setMinute(0)->setSecond(0);
        $rescheduleResponse = $this->patchJson("/api/admin/live-classes/{$liveClass->id}/reschedule", [
            'scheduled_at' => $newTime->toDateTimeString(),
            'duration_minutes' => 75,
            'reschedule_reason' => 'Postponed due to school sports day.',
        ]);
        $rescheduleResponse->assertStatus(200)
            ->assertJsonPath('data.duration_minutes', 75);

        // 2. Mark Live
        $liveResponse = $this->patchJson("/api/admin/live-classes/{$liveClass->id}/status", [
            'status' => 'live',
        ]);
        $liveResponse->assertStatus(200)
            ->assertJsonPath('data.status', 'live');

        // 3. Complete and record URL
        $completeResponse = $this->patchJson("/api/admin/live-classes/{$liveClass->id}/status", [
            'status' => 'completed',
            'recording_url' => 'https://www.youtube.com/watch?v=recFinal123',
        ]);
        $completeResponse->assertStatus(200)
            ->assertJsonPath('data.status', 'completed')
            ->assertJsonPath('data.recording_url', 'https://www.youtube.com/watch?v=recFinal123');

        // 4. Test Cancellation on a new class
        $classToCancel = LiveClass::create([
            'class_level' => 8,
            'subject_id' => $this->subject->id,
            'teacher_id' => $this->teacher->id,
            'title' => 'Class To Cancel',
            'platform' => 'google_meet',
            'meeting_url' => 'https://meet.google.com/xyz-uvwx-rst',
            'scheduled_at' => now()->addDays(5),
            'duration_minutes' => 60,
            'status' => 'scheduled',
        ]);

        $cancelResponse = $this->patchJson("/api/admin/live-classes/{$classToCancel->id}/cancel", [
            'reason' => 'Faculty medical emergency.',
        ]);
        $cancelResponse->assertStatus(200)
            ->assertJsonPath('data.status', 'cancelled')
            ->assertJsonPath('data.cancellation_reason', 'Faculty medical emergency.');
    }

    /**
     * Admin web panel page loads successfully for authenticated session.
     */
    public function test_admin_web_live_classes_view_renders(): void
    {
        $response = $this->withSession([
            'admin_token' => 'mock_sanctum_token',
            'admin_user' => ['id' => $this->admin->id, 'name' => 'Admin User', 'email' => 'admin@test.com', 'role' => 'admin'],
        ])->get('/admin/live-classes');

        $response->assertStatus(200);
        $response->assertSee('Live Classes Management');
        $response->assertSee('Total Live Classes');
    }
}
