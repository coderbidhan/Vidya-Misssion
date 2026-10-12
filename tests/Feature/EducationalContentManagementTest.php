<?php

namespace Tests\Feature;

use App\Models\Chapter;
use App\Models\ClassSubject;
use App\Models\Note;
use App\Models\Question;
use App\Models\RecordedClass;
use App\Models\Subject;
use App\Models\Test;
use App\Models\User;
use Database\Seeders\EducationalContentSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class EducationalContentManagementTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $student;
    protected User $teacher;

    protected function setUp(): void
    {
        parent::setUp();

        // Create standard test users
        $this->admin = User::create([
            'name' => 'System Admin',
            'email' => 'admin@test.com',
            'mobile' => '9999999999',
            'role' => 'admin',
            'password' => bcrypt('password'),
            'is_active' => true,
            'is_verified' => true,
        ]);

        $this->teacher = User::create([
            'name' => 'Faculty Teacher',
            'email' => 'teacher@test.com',
            'mobile' => '8888888888',
            'role' => 'teacher',
            'password' => bcrypt('password'),
            'is_active' => true,
            'is_verified' => true,
        ]);

        $this->student = User::create([
            'name' => 'Enrolled Student',
            'email' => 'student@test.com',
            'mobile' => '7777777777',
            'role' => 'student',
            'password' => bcrypt('password'),
            'trial_starts_at' => now(),
            'trial_ends_at' => now()->addDays(7),
            'is_active' => true,
            'is_verified' => true,
        ]);
    }

    /**
     * Test Academic Classes Discovery endpoint returns Class 1 through 10.
     */
    public function test_classes_discovery_endpoint_returns_classes_1_to_10(): void
    {
        $response = $this->getJson('/api/classes');

        $response->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonCount(10, 'data');

        $data = $response->json('data');
        $this->assertEquals(1, $data[0]['class_level']);
        $this->assertEquals('Class 1', $data[0]['name']);
        $this->assertEquals('WBBSE', $data[0]['board']);
        $this->assertEquals('Primary', $data[0]['stage']);

        $this->assertEquals(10, $data[9]['class_level']);
        $this->assertEquals('Class 10', $data[9]['name']);
        $this->assertEquals('Secondary (Madhyamik)', $data[9]['stage']);
    }

    /**
     * Test Subjects and Chapters mapped to a Class.
     */
    public function test_class_subjects_endpoint_returns_mapped_subjects_and_validates_level(): void
    {
        $subject = Subject::create([
            'name' => 'গণিত (Mathematics)',
            'color' => '#3B82F6',
            'icon' => 'calculator',
            'sort_order' => 1,
            'is_active' => true,
        ]);

        ClassSubject::create([
            'class_level' => 10,
            'subject_id' => $subject->id,
        ]);

        Chapter::create([
            'subject_id' => $subject->id,
            'class_level' => 10,
            'title' => 'একচল বিশিষ্ট দ্বিঘাত সমীকরণ',
            'sort_order' => 1,
            'is_active' => true,
        ]);

        $response = $this->getJson('/api/classes/10/subjects');

        $response->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.class_level', 10)
            ->assertJsonCount(1, 'data.subjects');

        // Test invalid class level returns 422
        $invalidResponse = $this->getJson('/api/classes/99/subjects');
        $invalidResponse->assertStatus(422)
            ->assertJsonPath('success', false);
    }

    /**
     * Test Swagger UI and OpenAPI 3.0.3 JSON endpoints.
     */
    public function test_swagger_and_openapi_endpoints_return_200_and_valid_spec(): void
    {
        // 1. Swagger UI view at /docs
        $uiResponse = $this->get('/docs');
        $uiResponse->assertStatus(200)
            ->assertSee('Vidya Mission API')
            ->assertSee('SwaggerUIBundle');

        // 2. OpenAPI specification at /docs/openapi.json
        $jsonResponse = $this->getJson('/docs/openapi.json');
        $jsonResponse->assertStatus(200)
            ->assertJsonPath('openapi', '3.0.3')
            ->assertJsonPath('info.title', 'Vidya Mission Educational Platform API');

        // Verify key paths exist in OpenAPI spec
        $paths = $jsonResponse->json('paths');
        $this->assertArrayHasKey('/api/classes', $paths);
        $this->assertArrayHasKey('/api/classes/{classLevel}/subjects', $paths);
        $this->assertArrayHasKey('/api/admin/recorded-classes', $paths);
        $this->assertArrayHasKey('/api/admin/notes', $paths);
        $this->assertArrayHasKey('/api/admin/tests', $paths);

        // 3. API route alias at /api/docs/openapi.json
        $apiAliasResponse = $this->getJson('/api/docs/openapi.json');
        $apiAliasResponse->assertStatus(200)
            ->assertJsonPath('openapi', '3.0.3');
    }

    /**
     * Test Educational Content Seeders for Class 1 to 10 are completely idempotent.
     */
    public function test_educational_content_seeders_run_and_are_idempotent(): void
    {
        // First run
        Artisan::call('db:seed', ['--class' => EducationalContentSeeder::class]);

        $subjectCountAfterFirst = Subject::count();
        $chapterCountAfterFirst = Chapter::count();
        $noteCountAfterFirst = Note::count();
        $recordedCountAfterFirst = RecordedClass::count();
        $testCountAfterFirst = Test::count();
        $questionCountAfterFirst = Question::count();

        // Verify Class 1 through 10 have chapters
        for ($class = 1; $class <= 10; $class++) {
            $this->assertGreaterThan(
                0,
                Chapter::where('class_level', $class)->count(),
                "Class {$class} should have seeded chapters."
            );
        }

        // Second run must be completely idempotent
        Artisan::call('db:seed', ['--class' => EducationalContentSeeder::class]);

        $this->assertEquals($subjectCountAfterFirst, Subject::count(), 'Subjects duplicated on second seed run.');
        $this->assertEquals($chapterCountAfterFirst, Chapter::count(), 'Chapters duplicated on second seed run.');
        $this->assertEquals($noteCountAfterFirst, Note::count(), 'Notes duplicated on second seed run.');
        $this->assertEquals($recordedCountAfterFirst, RecordedClass::count(), 'Recorded classes duplicated on second seed run.');
        $this->assertEquals($testCountAfterFirst, Test::count(), 'Tests duplicated on second seed run.');
        $this->assertEquals($questionCountAfterFirst, Question::count(), 'Questions duplicated on second seed run.');
    }

    /**
     * Test Admin Authorization: Only users with role 'admin' can access admin APIs.
     */
    public function test_admin_authorization_enforced(): void
    {
        // Unauthenticated -> 401
        $guestResponse = $this->getJson('/api/admin/recorded-classes');
        $guestResponse->assertStatus(401);

        // Authenticated student -> 403 Forbidden
        Sanctum::actingAs($this->student);
        $studentResponse = $this->getJson('/api/admin/recorded-classes');
        $studentResponse->assertStatus(403);

        // Authenticated admin -> 200 OK
        Sanctum::actingAs($this->admin);
        $adminResponse = $this->getJson('/api/admin/recorded-classes');
        $adminResponse->assertStatus(200);
    }

    /**
     * Test Admin content filtering and single resource retrieval.
     */
    public function test_admin_can_filter_content_and_fetch_single_items(): void
    {
        Sanctum::actingAs($this->admin);

        $math = Subject::create(['name' => 'Mathematics', 'color' => '#3B82F6', 'icon' => 'calculator', 'sort_order' => 1, 'is_active' => true]);
        $english = Subject::create(['name' => 'English', 'color' => '#10B981', 'icon' => 'book', 'sort_order' => 2, 'is_active' => true]);

        $ch10 = Chapter::create(['subject_id' => $math->id, 'class_level' => 10, 'title' => 'Math Class 10 Ch1', 'is_active' => true]);
        $ch5 = Chapter::create(['subject_id' => $english->id, 'class_level' => 5, 'title' => 'Eng Class 5 Ch1', 'is_active' => true]);

        $rec10 = RecordedClass::create([
            'chapter_id' => $ch10->id,
            'teacher_id' => $this->teacher->id,
            'title' => 'Class 10 Math Video',
            'gdrive_file_id' => 'rec_10',
            'gdrive_embed_url' => 'https://drive.google.com/file/d/rec_10/preview',
            'is_active' => true,
        ]);

        $rec5 = RecordedClass::create([
            'chapter_id' => $ch5->id,
            'teacher_id' => $this->teacher->id,
            'title' => 'Class 5 English Video',
            'gdrive_file_id' => 'rec_5',
            'gdrive_embed_url' => 'https://drive.google.com/file/d/rec_5/preview',
            'is_active' => true,
        ]);

        // Filter by class_level=10
        $filterClassResponse = $this->getJson('/api/admin/recorded-classes?class_level=10');
        $filterClassResponse->assertStatus(200);
        $data = $filterClassResponse->json('data');
        $this->assertCount(1, $data);
        $this->assertEquals($rec10->id, $data[0]['id']);

        // Fetch single recorded class
        $showResponse = $this->getJson("/api/admin/recorded-classes/{$rec10->id}");
        $showResponse->assertStatus(200)
            ->assertJsonPath('data.id', $rec10->id)
            ->assertJsonPath('data.title', 'Class 10 Math Video');

        // Test notes filtering & show
        $note10 = Note::create([
            'chapter_id' => $ch10->id,
            'teacher_id' => $this->teacher->id,
            'title' => 'Class 10 Math Revision Note',
            'file_url' => 'https://example.com/math10.pdf',
            'file_type' => 'pdf',
            'is_active' => true,
        ]);

        $noteShowResponse = $this->getJson("/api/admin/notes/{$note10->id}");
        $noteShowResponse->assertStatus(200)
            ->assertJsonPath('data.id', $note10->id);

        // Test test filtering & show
        $test10 = Test::create([
            'chapter_id' => $ch10->id,
            'title' => 'Class 10 Math Unit Test',
            'total_marks' => 10,
            'pass_marks' => 4,
            'duration_minutes' => 20,
            'is_active' => true,
        ]);

        $testShowResponse = $this->getJson("/api/admin/tests/{$test10->id}");
        $testShowResponse->assertStatus(200)
            ->assertJsonPath('data.id', $test10->id);
    }

    /**
     * Test Student Practice Test flow: hiding correct answers, submitting attempt, calculating score.
     */
    public function test_student_test_attempt_and_scoring(): void
    {
        $subject = Subject::create(['name' => 'Science', 'color' => '#10B981', 'icon' => 'atom', 'sort_order' => 1, 'is_active' => true]);
        $chapter = Chapter::create(['subject_id' => $subject->id, 'class_level' => 10, 'title' => 'Physics Ch1', 'is_active' => true]);

        $test = Test::create([
            'chapter_id' => $chapter->id,
            'title' => 'Forces and Motion Test',
            'total_marks' => 2,
            'pass_marks' => 1,
            'duration_minutes' => 15,
            'is_active' => true,
        ]);

        $q1 = Question::create([
            'test_id' => $test->id,
            'question_text' => 'What is the SI unit of force?',
            'option_a' => 'Joule',
            'option_b' => 'Newton',
            'option_c' => 'Watt',
            'option_d' => 'Pascal',
            'correct_option' => 'b',
            'marks' => 1,
            'sort_order' => 1,
        ]);

        $q2 = Question::create([
            'test_id' => $test->id,
            'question_text' => 'Acceleration due to gravity on Earth is approximately:',
            'option_a' => '9.8 m/s^2',
            'option_b' => '8.9 m/s^2',
            'option_c' => '10.8 m/s^2',
            'option_d' => '7.8 m/s^2',
            'correct_option' => 'a',
            'marks' => 1,
            'sort_order' => 2,
        ]);

        Sanctum::actingAs($this->student);

        // Fetch test as student - correct_option must NOT be visible
        $testResponse = $this->getJson("/api/tests/{$test->id}");
        $testResponse->assertStatus(200);
        $questions = $testResponse->json('data.questions');
        $this->assertCount(2, $questions);
        $this->assertArrayNotHasKey('correct_option', $questions[0]);

        // Submit test attempt: 1 correct (q1='b'), 1 wrong (q2='c')
        $attemptResponse = $this->postJson("/api/tests/{$test->id}/attempt", [
            'answers' => [
                $q1->id => 'b', // Correct
                $q2->id => 'c', // Wrong
            ],
            'time_taken_seconds' => 320,
        ]);

        $attemptResponse->assertStatus(200)
            ->assertJsonPath('data.score', 1)
            ->assertJsonPath('data.total_marks', 2)
            ->assertJsonPath('data.is_passed', true);
    }

    /**
     * Test inactive content is hidden from student endpoints.
     */
    public function test_inactive_content_hidden_from_students(): void
    {
        $subject = Subject::create(['name' => 'History', 'color' => '#F59E0B', 'icon' => 'globe', 'sort_order' => 1, 'is_active' => true]);
        $chapter = Chapter::create(['subject_id' => $subject->id, 'class_level' => 8, 'title' => 'History Ch1', 'is_active' => true]);

        RecordedClass::create([
            'chapter_id' => $chapter->id,
            'teacher_id' => $this->teacher->id,
            'title' => 'Draft Unfinished Video',
            'gdrive_file_id' => 'draft_vid',
            'gdrive_embed_url' => 'https://drive.google.com/file/d/draft_vid/preview',
            'is_active' => false, // INACTIVE
        ]);

        Note::create([
            'chapter_id' => $chapter->id,
            'teacher_id' => $this->teacher->id,
            'title' => 'Draft Rough Notes',
            'file_url' => 'https://example.com/draft.pdf',
            'file_type' => 'pdf',
            'is_active' => false, // INACTIVE
        ]);

        Sanctum::actingAs($this->student);

        // Student fetches recorded classes
        $recResponse = $this->getJson("/api/chapters/{$chapter->id}/recorded-classes");
        $recResponse->assertStatus(200)
            ->assertJsonCount(0, 'data');

        // Student fetches notes
        $noteResponse = $this->getJson("/api/chapters/{$chapter->id}/notes");
        $noteResponse->assertStatus(200)
            ->assertJsonCount(0, 'data');
    }
}
