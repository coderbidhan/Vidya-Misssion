<?php

namespace Tests\Feature;

use App\Models\Chapter;
use App\Models\ClassSubject;
use App\Models\LiveClass;
use App\Models\StudentProfile;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class StudentLiveClassApiTest extends TestCase
{
    use RefreshDatabase;

    protected User $subscribedStudent;

    protected User $unsubscribedStudent;

    protected User $teacher;

    protected Subject $subject;

    protected Chapter $chapter;

    protected function setUp(): void
    {
        parent::setUp();

        $this->teacher = User::create([
            'name' => 'Faculty Teacher',
            'email' => 'teacher@test.com',
            'mobile' => '8888880002',
            'role' => 'teacher',
            'password' => bcrypt('password'),
            'is_active' => true,
            'is_verified' => true,
        ]);

        // Student with active trial
        $this->subscribedStudent = User::create([
            'name' => 'Subscribed Student',
            'email' => 'student.active@test.com',
            'mobile' => '7777770002',
            'role' => 'student',
            'password' => bcrypt('password'),
            'trial_starts_at' => now()->subDay(),
            'trial_ends_at' => now()->addDays(5),
            'is_active' => true,
            'is_verified' => true,
        ]);
        StudentProfile::create([
            'user_id' => $this->subscribedStudent->id,
            'name' => 'Subscribed Student',
            'class' => 'Class 10',
            'school_name' => 'Kolkata High School',
            'district' => 'Kolkata',
        ]);

        // Student with expired trial and no subscription
        $this->unsubscribedStudent = User::create([
            'name' => 'Expired Student',
            'email' => 'student.expired@test.com',
            'mobile' => '7777770003',
            'role' => 'student',
            'password' => bcrypt('password'),
            'trial_starts_at' => now()->subDays(10),
            'trial_ends_at' => now()->subDays(3),
            'is_active' => true,
            'is_verified' => true,
        ]);
        StudentProfile::create([
            'user_id' => $this->unsubscribedStudent->id,
            'name' => 'Expired Student',
            'class' => 'Class 10',
            'school_name' => 'Howrah Model School',
            'district' => 'Howrah',
        ]);

        $this->subject = Subject::create([
            'name' => 'Physical Science',
            'color' => '#10B981',
            'icon' => 'atom',
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
            'title' => 'Light and Optics',
            'sort_order' => 1,
            'is_active' => true,
        ]);
    }

    /**
     * Backward compatibility check: GET /api/chapters/{id}/live-classes returns expected schema.
     */
    public function test_by_chapter_endpoint_preserves_contract_and_unlocks_meeting_url_for_subscribed_student(): void
    {
        $liveClass = LiveClass::create([
            'class_level' => 10,
            'subject_id' => $this->subject->id,
            'chapter_id' => $this->chapter->id,
            'teacher_id' => $this->teacher->id,
            'title' => 'Optics Numerical Solving',
            'description' => 'Light reflection laws and mirror formula problem solving.',
            'platform' => 'google_meet',
            'meeting_url' => 'https://meet.google.com/abc-defg-hij',
            'scheduled_at' => now()->addDays(1),
            'duration_minutes' => 60,
            'status' => 'scheduled',
            'is_published' => true,
        ]);

        Sanctum::actingAs($this->subscribedStudent);

        $response = $this->getJson("/api/chapters/{$this->chapter->id}/live-classes");

        $response->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.has_access', true)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'upcoming',
                    'live',
                    'past',
                    'has_access',
                ],
            ]);

        $upcoming = $response->json('data.upcoming');
        $this->assertCount(1, $upcoming);
        $this->assertEquals($liveClass->id, $upcoming[0]['id']);
        $this->assertEquals('https://meet.google.com/abc-defg-hij', $upcoming[0]['meeting_url']);
        $this->assertFalse($upcoming[0]['is_locked']);
    }

    /**
     * Access gating: meeting_url is hidden and locked for expired / unsubscribed student.
     */
    public function test_meeting_url_is_hidden_when_student_has_no_access(): void
    {
        LiveClass::create([
            'class_level' => 10,
            'subject_id' => $this->subject->id,
            'chapter_id' => $this->chapter->id,
            'teacher_id' => $this->teacher->id,
            'title' => 'Optics Premium Session',
            'platform' => 'google_meet',
            'meeting_url' => 'https://meet.google.com/secret-meeting-link',
            'scheduled_at' => now()->addDays(1),
            'duration_minutes' => 60,
            'status' => 'scheduled',
            'is_published' => true,
        ]);

        Sanctum::actingAs($this->unsubscribedStudent);

        $response = $this->getJson("/api/chapters/{$this->chapter->id}/live-classes");

        $response->assertStatus(200)
            ->assertJsonPath('data.has_access', false);

        $upcoming = $response->json('data.upcoming');
        $this->assertCount(1, $upcoming);
        $this->assertNull($upcoming[0]['meeting_url'], 'Private meeting URL must be null for locked users.');
        $this->assertTrue($upcoming[0]['is_locked']);
    }

    /**
     * Student can list live classes by grade and subject via GET /api/live-classes.
     */
    public function test_student_can_fetch_live_classes_by_grade(): void
    {
        LiveClass::create([
            'class_level' => 10,
            'subject_id' => $this->subject->id,
            'teacher_id' => $this->teacher->id,
            'title' => 'Class 10 Optics Session',
            'platform' => 'google_meet',
            'meeting_url' => 'https://meet.google.com/abc-defg-hij',
            'scheduled_at' => now()->addDays(2),
            'duration_minutes' => 60,
            'status' => 'scheduled',
            'is_published' => true,
        ]);

        LiveClass::create([
            'class_level' => 7,
            'subject_id' => $this->subject->id,
            'teacher_id' => $this->teacher->id,
            'title' => 'Class 7 General Science',
            'platform' => 'google_meet',
            'meeting_url' => 'https://meet.google.com/xyz-uvwx-rst',
            'scheduled_at' => now()->addDays(3),
            'duration_minutes' => 45,
            'status' => 'scheduled',
            'is_published' => true,
        ]);

        Sanctum::actingAs($this->subscribedStudent); // Profile is enrolled in Class 10

        // Calling without query automatically defaults to student's enrolled Class 10
        $response = $this->getJson('/api/live-classes');

        $response->assertStatus(200)
            ->assertJsonPath('success', true);

        $data = $response->json('data');
        $this->assertCount(1, $data);
        $this->assertEquals('Class 10 Optics Session', $data[0]['title']);
    }

    /**
     * Currently active sessions endpoint: GET /api/live-classes/now.
     */
    public function test_student_can_fetch_currently_live_sessions(): void
    {
        LiveClass::create([
            'class_level' => 10,
            'subject_id' => $this->subject->id,
            'teacher_id' => $this->teacher->id,
            'title' => 'Optics LIVE Q&A Now',
            'platform' => 'google_meet',
            'meeting_url' => 'https://meet.google.com/now-live-session',
            'scheduled_at' => now()->subMinutes(10),
            'duration_minutes' => 60,
            'status' => 'live',
            'is_published' => true,
        ]);

        LiveClass::create([
            'class_level' => 10,
            'subject_id' => $this->subject->id,
            'teacher_id' => $this->teacher->id,
            'title' => 'Optics Scheduled Future',
            'platform' => 'google_meet',
            'meeting_url' => 'https://meet.google.com/future-meeting',
            'scheduled_at' => now()->addDays(1),
            'duration_minutes' => 60,
            'status' => 'scheduled',
            'is_published' => true,
        ]);

        Sanctum::actingAs($this->subscribedStudent);

        $response = $this->getJson('/api/live-classes/now');

        $response->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.title', 'Optics LIVE Q&A Now')
            ->assertJsonPath('data.0.status', 'live');
    }

    /**
     * Single live class show endpoint with subscription check.
     */
    public function test_single_live_class_show_endpoint(): void
    {
        $liveClass = LiveClass::create([
            'class_level' => 10,
            'subject_id' => $this->subject->id,
            'teacher_id' => $this->teacher->id,
            'title' => 'Single Class Show Test',
            'description' => 'Detailed curriculum discussion.',
            'learning_objectives' => 'Understand Snell’s law.',
            'platform' => 'google_meet',
            'meeting_url' => 'https://meet.google.com/single-show-code',
            'scheduled_at' => now()->addDays(2),
            'duration_minutes' => 60,
            'status' => 'scheduled',
            'is_published' => true,
        ]);

        // 1. Subscribed student receives full meeting URL
        Sanctum::actingAs($this->subscribedStudent);
        $resSub = $this->getJson("/api/live-classes/{$liveClass->id}");
        $resSub->assertStatus(200)
            ->assertJsonPath('data.meeting_url', 'https://meet.google.com/single-show-code')
            ->assertJsonPath('data.is_locked', false)
            ->assertJsonPath('data.learning_objectives', 'Understand Snell’s law.');

        // 2. Unsubscribed student receives null meeting URL with subscription_required flag
        Sanctum::actingAs($this->unsubscribedStudent);
        $resUnsub = $this->getJson("/api/live-classes/{$liveClass->id}");
        $resUnsub->assertStatus(200)
            ->assertJsonPath('data.meeting_url', null)
            ->assertJsonPath('data.is_locked', true)
            ->assertJsonPath('data.subscription_required', true);
    }

    /**
     * Unpublished and cancelled sessions are hidden from students.
     */
    public function test_unpublished_and_cancelled_classes_are_hidden_from_students(): void
    {
        LiveClass::create([
            'class_level' => 10,
            'subject_id' => $this->subject->id,
            'teacher_id' => $this->teacher->id,
            'title' => 'Draft Unpublished Class',
            'platform' => 'google_meet',
            'meeting_url' => 'https://meet.google.com/draft-meet',
            'scheduled_at' => now()->addDays(2),
            'duration_minutes' => 60,
            'status' => 'scheduled',
            'is_published' => false, // DRAFT
        ]);

        LiveClass::create([
            'class_level' => 10,
            'subject_id' => $this->subject->id,
            'teacher_id' => $this->teacher->id,
            'title' => 'Cancelled Class',
            'platform' => 'google_meet',
            'meeting_url' => 'https://meet.google.com/cancelled-meet',
            'scheduled_at' => now()->addDays(3),
            'duration_minutes' => 60,
            'status' => 'cancelled', // CANCELLED
            'is_published' => true,
        ]);

        Sanctum::actingAs($this->subscribedStudent);

        $response = $this->getJson('/api/live-classes');
        $response->assertStatus(200)
            ->assertJsonCount(0, 'data');
    }
}
