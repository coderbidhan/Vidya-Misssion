<?php

namespace Tests\Feature;

use App\Models\Chapter;
use App\Models\ClassSubject;
use App\Models\RecordedClass;
use App\Models\Subject;
use App\Models\User;
use App\Services\YouTubeService;
use Database\Seeders\EducationalContentSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class RecordedClassVideoUrlTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $student;
    protected User $teacher;
    protected Chapter $chapter;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@test.com',
            'mobile' => '9999999991',
            'role' => 'admin',
            'password' => bcrypt('password'),
            'is_active' => true,
            'is_verified' => true,
        ]);

        $this->teacher = User::create([
            'name' => 'Faculty Teacher',
            'email' => 'teacher@test.com',
            'mobile' => '8888888881',
            'role' => 'teacher',
            'password' => bcrypt('password'),
            'is_active' => true,
            'is_verified' => true,
        ]);

        $this->student = User::create([
            'name' => 'Active Student',
            'email' => 'student@test.com',
            'mobile' => '7777777771',
            'role' => 'student',
            'password' => bcrypt('password'),
            'trial_starts_at' => now(),
            'trial_ends_at' => now()->addDays(7),
            'is_active' => true,
            'is_verified' => true,
        ]);

        $subject = Subject::create([
            'name' => 'Mathematics',
            'color' => '#3B82F6',
            'icon' => 'calculator',
            'sort_order' => 1,
            'is_active' => true,
        ]);

        ClassSubject::create([
            'class_level' => 10,
            'subject_id' => $subject->id,
        ]);

        $this->chapter = Chapter::create([
            'subject_id' => $subject->id,
            'class_level' => 10,
            'title' => 'Quadratic Equations',
            'sort_order' => 1,
            'is_active' => true,
        ]);
    }

    /**
     * Requirement 1: Seeders for Class 1-10 populate valid YouTube video URLs.
     */
    public function test_educational_content_seeder_populates_valid_youtube_urls_for_classes_1_to_10(): void
    {
        Artisan::call('db:seed', ['--class' => EducationalContentSeeder::class]);

        for ($classLevel = 1; $classLevel <= 10; $classLevel++) {
            $recordedClasses = RecordedClass::whereHas('chapter', function ($q) use ($classLevel) {
                $q->where('class_level', $classLevel);
            })->get();

            $this->assertGreaterThan(
                0,
                $recordedClasses->count(),
                "Class {$classLevel} should have seeded recorded classes."
            );

            foreach ($recordedClasses as $rc) {
                $this->assertNotEmpty($rc->video_url, "Recorded class '{$rc->title}' must have a video_url.");
                $this->assertTrue(
                    YouTubeService::isValidYouTubeUrl($rc->video_url),
                    "Video URL '{$rc->video_url}' for class {$classLevel} must be a valid YouTube URL."
                );
                $this->assertStringStartsWith(
                    'https://www.youtube.com/watch?v=',
                    $rc->video_url,
                    "Video URL should be normalized to standard watch URL."
                );
                $this->assertNotNull($rc->chapter_id);
                $this->assertNotNull($rc->teacher_id);

                // Ensure no dummy Rickroll placeholder in seeded data
                $this->assertNotEquals('dQw4w9WgXcQ', $rc->gdrive_file_id, "Seeded data must not use placeholder Rickroll ID.");
            }
        }
    }

    /**
     * Requirement 1: Seeder is idempotent and safe to rerun.
     */
    public function test_educational_content_seeder_is_idempotent(): void
    {
        Artisan::call('db:seed', ['--class' => EducationalContentSeeder::class]);
        $countFirst = RecordedClass::count();

        // Run seeder a second time
        Artisan::call('db:seed', ['--class' => EducationalContentSeeder::class]);
        $countSecond = RecordedClass::count();

        $this->assertEquals($countFirst, $countSecond, "Rerunning seeder must not create duplicate recorded classes.");
    }

    /**
     * Requirement 1: Seeder preserves administrator custom recorded classes.
     */
    public function test_seeder_preserves_custom_admin_recorded_class(): void
    {
        Artisan::call('db:seed', ['--class' => EducationalContentSeeder::class]);

        $customClass = RecordedClass::create([
            'chapter_id' => $this->chapter->id,
            'teacher_id' => $this->teacher->id,
            'title' => 'Custom Administrator Special Class',
            'description' => 'Created by admin directly in admin panel',
            'video_url' => 'https://www.youtube.com/watch?v=E7wJTI-1dqQ',
            'duration_seconds' => 2400,
            'is_active' => true,
        ]);

        Artisan::call('db:seed', ['--class' => EducationalContentSeeder::class]);

        $this->assertDatabaseHas('recorded_classes', [
            'id' => $customClass->id,
            'title' => 'Custom Administrator Special Class',
            'video_url' => 'https://www.youtube.com/watch?v=E7wJTI-1dqQ',
        ]);
    }

    /**
     * Requirement 2: Existing API returns YouTube URL through the existing video_url key without changing structure.
     */
    public function test_student_api_returns_youtube_url_in_video_url_key_when_accessible(): void
    {
        $rc = RecordedClass::create([
            'chapter_id' => $this->chapter->id,
            'teacher_id' => $this->teacher->id,
            'title' => 'Test Quadratic Formula',
            'description' => 'Deriving quadratic roots',
            'video_url' => 'https://www.youtube.com/watch?v=E7wJTI-1dqQ',
            'duration_seconds' => 1800,
            'is_active' => true,
        ]);

        Sanctum::actingAs($this->student);

        // Chapter list endpoint
        $response = $this->getJson("/api/chapters/{$this->chapter->id}/recorded-classes");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    '*' => [
                        'id',
                        'chapter_id',
                        'teacher_id',
                        'title',
                        'description',
                        'video_url',
                        'gdrive_embed_url',
                        'thumbnail_url',
                        'duration_seconds',
                        'is_locked',
                        'teacher' => ['id', 'name'],
                    ]
                ]
            ]);

        $item = collect($response->json('data'))->firstWhere('id', $rc->id);
        $this->assertNotNull($item);
        $this->assertEquals('https://www.youtube.com/watch?v=E7wJTI-1dqQ', $item['video_url']);
        $this->assertFalse($item['is_locked']);
        $this->assertNotEmpty($item['gdrive_embed_url']);

        // Single show endpoint
        $showResponse = $this->getJson("/api/recorded-classes/{$rc->id}");
        $showResponse->assertStatus(200)
            ->assertJsonPath('data.id', $rc->id)
            ->assertJsonPath('data.video_url', 'https://www.youtube.com/watch?v=E7wJTI-1dqQ')
            ->assertJsonPath('data.is_locked', false);
    }

    /**
     * Requirement 2: video_url is null and is_locked is true when trial has expired and student has no subscription.
     */
    public function test_student_api_returns_null_video_url_when_trial_expired(): void
    {
        $rc = RecordedClass::create([
            'chapter_id' => $this->chapter->id,
            'teacher_id' => $this->teacher->id,
            'title' => 'Locked Lesson',
            'video_url' => 'https://www.youtube.com/watch?v=E7wJTI-1dqQ',
            'is_active' => true,
        ]);

        $expiredStudent = User::create([
            'name' => 'Expired Student',
            'email' => 'expired@test.com',
            'mobile' => '7777777772',
            'role' => 'student',
            'password' => bcrypt('password'),
            'trial_starts_at' => now()->subDays(10),
            'trial_ends_at' => now()->subDays(3),
            'is_active' => true,
            'is_verified' => true,
        ]);

        Sanctum::actingAs($expiredStudent);

        $response = $this->getJson("/api/chapters/{$this->chapter->id}/recorded-classes");
        $response->assertStatus(200);

        $item = collect($response->json('data'))->firstWhere('id', $rc->id);
        $this->assertNotNull($item);
        $this->assertTrue($item['is_locked']);
        $this->assertNull($item['video_url']);
        $this->assertNull($item['gdrive_embed_url']);

        $showResponse = $this->getJson("/api/recorded-classes/{$rc->id}");
        $showResponse->assertStatus(200)
            ->assertJsonPath('data.is_locked', true)
            ->assertJsonPath('data.video_url', null)
            ->assertJsonPath('data.gdrive_embed_url', null);
    }

    /**
     * Requirement 3 & Phase 4: Admin can create recorded class with valid YouTube URL.
     */
    public function test_admin_can_create_recorded_class_with_youtube_url(): void
    {
        Sanctum::actingAs($this->admin);

        $payload = [
            'chapter_id' => $this->chapter->id,
            'teacher_id' => $this->teacher->id,
            'title' => 'Admin Created Lecture',
            'description' => 'Detailed lecture description',
            'video_url' => 'https://www.youtube.com/watch?v=E7wJTI-1dqQ',
            'duration_seconds' => 1500,
            'is_active' => true,
        ];

        $response = $this->postJson('/api/admin/recorded-classes', $payload);

        $response->assertStatus(201)
            ->assertJsonPath('data.title', 'Admin Created Lecture')
            ->assertJsonPath('data.video_url', 'https://www.youtube.com/watch?v=E7wJTI-1dqQ');

        $createdId = $response->json('data.id');
        $this->assertDatabaseHas('recorded_classes', [
            'id' => $createdId,
            'video_url' => 'https://www.youtube.com/watch?v=E7wJTI-1dqQ',
            'gdrive_file_id' => 'E7wJTI-1dqQ',
            'gdrive_embed_url' => 'https://www.youtube.com/embed/E7wJTI-1dqQ',
        ]);
    }

    /**
     * Requirement 3 & Phase 4: Admin can update recorded class video URL.
     */
    public function test_admin_can_update_recorded_class_video_url(): void
    {
        $rc = RecordedClass::create([
            'chapter_id' => $this->chapter->id,
            'teacher_id' => $this->teacher->id,
            'title' => 'Original Title',
            'video_url' => 'https://www.youtube.com/watch?v=E7wJTI-1dqQ',
            'is_active' => true,
        ]);

        Sanctum::actingAs($this->admin);

        // Update with short youtu.be format
        $response = $this->putJson("/api/admin/recorded-classes/{$rc->id}", [
            'chapter_id' => $this->chapter->id,
            'teacher_id' => $this->teacher->id,
            'title' => 'Updated Title',
            'video_url' => 'https://youtu.be/M7lc1UVf-VE',
        ]);

        $response->assertStatus(200);

        $rc->refresh();
        $this->assertEquals('https://www.youtube.com/watch?v=M7lc1UVf-VE', $rc->video_url);
        $this->assertEquals('M7lc1UVf-VE', $rc->gdrive_file_id);
        $this->assertEquals('https://www.youtube.com/embed/M7lc1UVf-VE', $rc->gdrive_embed_url);
    }

    /**
     * Requirement 3 & Phase 4: Admin can remove recorded class video URL.
     */
    public function test_admin_can_remove_recorded_class_video_url(): void
    {
        $rc = RecordedClass::create([
            'chapter_id' => $this->chapter->id,
            'teacher_id' => $this->teacher->id,
            'title' => 'Original Class with Video',
            'video_url' => 'https://www.youtube.com/watch?v=E7wJTI-1dqQ',
            'is_active' => true,
        ]);

        Sanctum::actingAs($this->admin);

        $response = $this->putJson("/api/admin/recorded-classes/{$rc->id}", [
            'chapter_id' => $this->chapter->id,
            'teacher_id' => $this->teacher->id,
            'title' => 'Original Class with Video Removed',
            'video_url' => null,
            'gdrive_file_id' => null,
        ]);

        $response->assertStatus(200);

        $rc->refresh();
        $this->assertNull($rc->video_url);
    }

    /**
     * Requirement 3 & Phase 5: Admin validation rejects invalid YouTube URLs.
     */
    public function test_admin_validation_rejects_invalid_video_urls(): void
    {
        Sanctum::actingAs($this->admin);

        // Non-YouTube URL
        $response = $this->postJson('/api/admin/recorded-classes', [
            'chapter_id' => $this->chapter->id,
            'teacher_id' => $this->teacher->id,
            'title' => 'Invalid Video URL Class',
            'video_url' => 'https://vimeo.com/987654321',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['video_url']);

        // Malformed URL / arbitrary string
        $response2 = $this->postJson('/api/admin/recorded-classes', [
            'chapter_id' => $this->chapter->id,
            'teacher_id' => $this->teacher->id,
            'title' => 'Invalid Video URL Class',
            'video_url' => 'not-a-valid-url-at-all',
        ]);

        $response2->assertStatus(422)
            ->assertJsonValidationErrors(['video_url']);
    }

    /**
     * Security & Authorization: Students and guests cannot modify recorded classes.
     */
    public function test_unauthorized_users_cannot_modify_recorded_classes(): void
    {
        // Guest attempt
        $guestResponse = $this->postJson('/api/admin/recorded-classes', [
            'chapter_id' => $this->chapter->id,
            'title' => 'Hacked Class',
            'video_url' => 'https://www.youtube.com/watch?v=E7wJTI-1dqQ',
        ]);
        $guestResponse->assertStatus(401);

        // Student attempt
        Sanctum::actingAs($this->student);
        $studentResponse = $this->postJson('/api/admin/recorded-classes', [
            'chapter_id' => $this->chapter->id,
            'title' => 'Student Class',
            'video_url' => 'https://www.youtube.com/watch?v=E7wJTI-1dqQ',
        ]);
        $studentResponse->assertStatus(403);
    }
}
