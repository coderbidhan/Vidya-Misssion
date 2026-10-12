<?php

use App\Models\Chapter;
use App\Models\ClassSubject;
use App\Models\LiveClass;
use App\Models\Note;
use App\Models\Question;
use App\Models\RecordedClass;
use App\Models\StudentProfile;
use App\Models\Subject;
use App\Models\Subscription;
use App\Models\Test;
use App\Models\User;
use App\Models\UserSubscription;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    // 1. Create Subject
    $this->subject = Subject::create([
        'name' => 'Mathematics',
        'color' => '#3B82F6',
        'icon' => 'calculator',
        'sort_order' => 1,
        'is_active' => true,
    ]);

    // 2. Map Subject to Class 10
    ClassSubject::create([
        'class_level' => 10,
        'subject_id' => $this->subject->id,
    ]);

    // 3. Create Chapter
    $this->chapter = Chapter::create([
        'subject_id' => $this->subject->id,
        'title' => 'Quadratic Equations',
        'class_level' => 10,
        'sort_order' => 1,
        'is_active' => true,
    ]);

    // 4. Create Teacher User
    $this->teacher = User::create([
        'name' => 'Prof. Ramanujan',
        'email' => 'teacher@example.com',
        'mobile' => '9876543219',
        'role' => 'teacher',
        'password' => bcrypt('password'),
        'is_active' => true,
        'is_verified' => true,
    ]);

    // 5. Create Recorded Class
    $this->recordedClass = RecordedClass::create([
        'chapter_id' => $this->chapter->id,
        'teacher_id' => $this->teacher->id,
        'title' => 'Intro to Quadratics',
        'description' => 'Basic concepts and formulas',
        'gdrive_file_id' => 'gdrive123',
        'gdrive_embed_url' => 'https://drive.google.com/file/d/gdrive123/preview',
        'thumbnail_url' => 'https://example.com/thumb.jpg',
        'duration_seconds' => 1800,
        'is_active' => true,
    ]);

    // 6. Create Live Class
    $this->liveClass = LiveClass::create([
        'chapter_id' => $this->chapter->id,
        'teacher_id' => $this->teacher->id,
        'title' => 'Live Doubt Clearing',
        'description' => 'Q&A session',
        'platform' => 'zoom',
        'meeting_url' => 'https://zoom.us/j/123456789',
        'scheduled_at' => now()->addDay(),
        'duration_minutes' => 60,
        'status' => 'scheduled',
    ]);

    // 7. Create Note
    $this->note = Note::create([
        'chapter_id' => $this->chapter->id,
        'teacher_id' => $this->teacher->id,
        'title' => 'Quadratic Formula Cheatsheet',
        'description' => 'Summary notes for revision',
        'file_url' => 'https://example.com/notes/quadratics.pdf',
        'file_type' => 'pdf',
        'is_active' => true,
    ]);

    // 8. Create Test & Question
    $this->test = Test::create([
        'chapter_id' => $this->chapter->id,
        'title' => 'Quadratic Equations Chapter Test',
        'total_marks' => 10,
        'pass_marks' => 5,
        'duration_minutes' => 30,
        'is_active' => true,
    ]);

    $this->question = Question::create([
        'test_id' => $this->test->id,
        'question_text' => 'What is the discriminant of ax^2 + bx + c = 0?',
        'option_a' => 'b^2 - 4ac',
        'option_b' => 'b^2 + 4ac',
        'option_c' => '2a - 4bc',
        'option_d' => 'b / 2a',
        'correct_option' => 'a',
        'marks' => 10,
        'sort_order' => 1,
    ]);

    // 9. Create Subscription Plan
    $this->plan = Subscription::create([
        'name' => 'Annual Pro Plan',
        'price' => 999.00,
        'duration_days' => 365,
        'description' => 'Full 1 year access to all courses and tests',
        'is_active' => true,
    ]);
});

test('student in active 3-day trial can fetch all apis and view course content & tests', function () {
    // Student within 3-day trial
    $student = User::create([
        'name' => 'Active Trial Student',
        'mobile' => '9876543201',
        'password' => bcrypt('password123'),
        'role' => 'student',
        'trial_starts_at' => now(),
        'trial_ends_at' => now()->addDays(3),
        'is_active' => true,
        'is_verified' => true,
    ]);

    StudentProfile::create([
        'user_id' => $student->id,
        'name' => 'Active Trial Student',
        'class' => 10,
        'district' => 'Kolkata',
    ]);

    $token = $student->createToken('test-token')->plainTextToken;
    $headers = ['Authorization' => "Bearer {$token}"];

    // 1. Fetch Subjects
    $response = $this->withHeaders($headers)->getJson(route('subjects.index'));
    $response->assertStatus(200)
        ->assertJsonPath('success', true)
        ->assertJsonCount(1, 'data');

    // 2. Fetch Chapters for Subject
    $response = $this->withHeaders($headers)->getJson(route('subjects.chapters', $this->subject->id));
    $response->assertStatus(200)
        ->assertJsonPath('data.has_access', true)
        ->assertJsonPath('data.chapters.0.is_locked', false);

    // 3. Fetch Chapter Detail
    $response = $this->withHeaders($headers)->getJson(route('chapters.show', $this->chapter->id));
    $response->assertStatus(200)
        ->assertJsonPath('data.has_access', true)
        ->assertJsonPath('data.is_locked', false);

    // 4. Fetch Recorded Classes -> embed url is present!
    $response = $this->withHeaders($headers)->getJson(route('recorded-classes.by-chapter', $this->chapter->id));
    $response->assertStatus(200)
        ->assertJsonPath('data.0.is_locked', false)
        ->assertJsonPath('data.0.gdrive_embed_url', 'https://drive.google.com/file/d/gdrive123/preview');

    // 5. Fetch Recorded Class Detail -> embed url is present!
    $response = $this->withHeaders($headers)->getJson(route('recorded-classes.show', $this->recordedClass->id));
    $response->assertStatus(200)
        ->assertJsonPath('data.is_locked', false)
        ->assertJsonPath('data.gdrive_embed_url', 'https://drive.google.com/file/d/gdrive123/preview');

    // 6. Fetch Live Classes -> meeting url is present!
    $response = $this->withHeaders($headers)->getJson(route('live-classes.by-chapter', $this->chapter->id));
    $response->assertStatus(200)
        ->assertJsonPath('data.has_access', true)
        ->assertJsonPath('data.upcoming.0.meeting_url', 'https://zoom.us/j/123456789');

    // 7. Fetch Notes -> file url is present!
    $response = $this->withHeaders($headers)->getJson(route('notes.show', $this->note->id));
    $response->assertStatus(200)
        ->assertJsonPath('data.is_locked', false)
        ->assertJsonPath('data.file_url', 'https://example.com/notes/quadratics.pdf');

    // 8. Fetch Test Detail -> questions are visible!
    $response = $this->withHeaders($headers)->getJson(route('tests.show', $this->test->id));
    $response->assertStatus(200)
        ->assertJsonPath('data.is_locked', false)
        ->assertJsonPath('data.has_access', true)
        ->assertJsonCount(1, 'data.questions');

    // 9. Attempt Test -> succeeds!
    $response = $this->withHeaders($headers)->postJson(route('tests.attempt', $this->test->id), [
        'answers' => [(string) $this->question->id => 'a'],
    ]);
    $response->assertStatus(200)
        ->assertJsonPath('data.is_passed', true)
        ->assertJsonPath('data.score', 10);

    // 10. Complete Chapter -> succeeds!
    $response = $this->withHeaders($headers)->postJson(route('chapters.complete', $this->chapter->id));
    $response->assertStatus(200)
        ->assertJsonPath('data.completed', true);
});

test('when 3 days trial expires all apis data is fetched but course content and test questions are hidden and protected', function () {
    // Student whose 3-day trial expired 1 day ago (and has no paid subscription)
    $student = User::create([
        'name' => 'Expired Trial Student',
        'mobile' => '9876543202',
        'password' => bcrypt('password123'),
        'role' => 'student',
        'trial_starts_at' => now()->subDays(4),
        'trial_ends_at' => now()->subDays(1),
        'is_active' => true,
        'is_verified' => true,
    ]);

    StudentProfile::create([
        'user_id' => $student->id,
        'name' => 'Expired Trial Student',
        'class' => 10,
        'district' => 'Kolkata',
    ]);

    $token = $student->createToken('test-token')->plainTextToken;
    $headers = ['Authorization' => "Bearer {$token}"];

    // 1. Fetch Subjects -> succeeds (not blocked by 403!)
    $response = $this->withHeaders($headers)->getJson(route('subjects.index'));
    $response->assertStatus(200)
        ->assertJsonPath('success', true)
        ->assertJsonCount(1, 'data');

    // 2. Fetch Chapters for Subject -> succeeds, marked is_locked: true
    $response = $this->withHeaders($headers)->getJson(route('subjects.chapters', $this->subject->id));
    $response->assertStatus(200)
        ->assertJsonPath('data.has_access', false)
        ->assertJsonPath('data.chapters.0.is_locked', true);

    // 3. Fetch Chapter Detail -> succeeds, marked is_locked: true, has_access: false
    $response = $this->withHeaders($headers)->getJson(route('chapters.show', $this->chapter->id));
    $response->assertStatus(200)
        ->assertJsonPath('data.has_access', false)
        ->assertJsonPath('data.is_locked', true);

    // 4. Fetch Recorded Classes -> succeeds, but gdrive_embed_url is NULL and is_locked is true!
    $response = $this->withHeaders($headers)->getJson(route('recorded-classes.by-chapter', $this->chapter->id));
    $response->assertStatus(200)
        ->assertJsonPath('data.0.is_locked', true)
        ->assertJsonPath('data.0.gdrive_embed_url', null)
        ->assertJsonPath('data.0.title', 'Intro to Quadratics');

    // 5. Fetch Recorded Class Detail -> succeeds, but gdrive_embed_url is NULL and is_locked is true!
    $response = $this->withHeaders($headers)->getJson(route('recorded-classes.show', $this->recordedClass->id));
    $response->assertStatus(200)
        ->assertJsonPath('data.is_locked', true)
        ->assertJsonPath('data.gdrive_embed_url', null)
        ->assertJsonPath('data.subscription_required', true);

    // 6. Fetch Live Classes -> succeeds, but meeting_url is NULL and is_locked is true!
    $response = $this->withHeaders($headers)->getJson(route('live-classes.by-chapter', $this->chapter->id));
    $response->assertStatus(200)
        ->assertJsonPath('data.has_access', false)
        ->assertJsonPath('data.upcoming.0.is_locked', true)
        ->assertJsonPath('data.upcoming.0.meeting_url', null);

    // 7. Fetch Notes List -> succeeds, marked is_locked: true
    $response = $this->withHeaders($headers)->getJson(route('notes.by-chapter', $this->chapter->id));
    $response->assertStatus(200)
        ->assertJsonPath('data.0.is_locked', true);

    // 8. Fetch Note Detail -> succeeds, but file_url is NULL and is_locked is true!
    $response = $this->withHeaders($headers)->getJson(route('notes.show', $this->note->id));
    $response->assertStatus(200)
        ->assertJsonPath('data.is_locked', true)
        ->assertJsonPath('data.file_url', null)
        ->assertJsonPath('data.subscription_required', true);

    // 9. Fetch Tests List -> succeeds, marked is_locked: true
    $response = $this->withHeaders($headers)->getJson(route('tests.by-chapter', $this->chapter->id));
    $response->assertStatus(200)
        ->assertJsonPath('data.0.is_locked', true);

    // 10. Fetch Test Detail -> questions are NOT shown (questions: [])!
    $response = $this->withHeaders($headers)->getJson(route('tests.show', $this->test->id));
    $response->assertStatus(200)
        ->assertJsonPath('data.is_locked', true)
        ->assertJsonPath('data.subscription_required', true)
        ->assertJsonPath('data.questions', []);

    // 11. Attempting Test -> blocked with 403 TRIAL_EXPIRED!
    $response = $this->withHeaders($headers)->postJson(route('tests.attempt', $this->test->id), [
        'answers' => [(string) $this->question->id => 'a'],
    ]);
    $response->assertStatus(403)
        ->assertJsonPath('success', false)
        ->assertJsonPath('code', 'TRIAL_EXPIRED')
        ->assertJsonPath('subscription_required', true);

    // 12. Completing Chapter -> blocked with 403 TRIAL_EXPIRED!
    $response = $this->withHeaders($headers)->postJson(route('chapters.complete', $this->chapter->id));
    $response->assertStatus(403)
        ->assertJsonPath('success', false)
        ->assertJsonPath('code', 'TRIAL_EXPIRED')
        ->assertJsonPath('subscription_required', true);
});

test('when user subscribes to any plan they can view all course content, tests, and use all features', function () {
    // Student whose 3-day trial expired, but now has an active paid subscription
    $student = User::create([
        'name' => 'Subscribed Student',
        'mobile' => '9876543203',
        'password' => bcrypt('password123'),
        'role' => 'student',
        'trial_starts_at' => now()->subDays(10),
        'trial_ends_at' => now()->subDays(7),
        'is_active' => true,
        'is_verified' => true,
    ]);

    StudentProfile::create([
        'user_id' => $student->id,
        'name' => 'Subscribed Student',
        'class' => 10,
        'district' => 'Kolkata',
    ]);

    // Active paid subscription
    UserSubscription::create([
        'user_id' => $student->id,
        'subscription_id' => $this->plan->id,
        'payment_id' => 'pay_test_active_123',
        'payment_gateway' => 'razorpay',
        'payment_status' => 'completed',
        'starts_at' => now()->subDay(),
        'ends_at' => now()->addYear(),
    ]);

    $token = $student->createToken('test-token')->plainTextToken;
    $headers = ['Authorization' => "Bearer {$token}"];

    // 1. User access check helpers
    expect($student->isInTrial())->toBeFalse()
        ->and($student->isTrialExpired())->toBeTrue()
        ->and($student->hasActiveSubscription())->toBeTrue()
        ->and($student->hasAccess())->toBeTrue();

    // 2. Fetch Recorded Classes -> gdrive_embed_url is shown!
    $response = $this->withHeaders($headers)->getJson(route('recorded-classes.by-chapter', $this->chapter->id));
    $response->assertStatus(200)
        ->assertJsonPath('data.0.is_locked', false)
        ->assertJsonPath('data.0.gdrive_embed_url', 'https://drive.google.com/file/d/gdrive123/preview');

    // 3. Fetch Live Classes -> meeting_url is shown!
    $response = $this->withHeaders($headers)->getJson(route('live-classes.by-chapter', $this->chapter->id));
    $response->assertStatus(200)
        ->assertJsonPath('data.has_access', true)
        ->assertJsonPath('data.upcoming.0.is_locked', false)
        ->assertJsonPath('data.upcoming.0.meeting_url', 'https://zoom.us/j/123456789');

    // 4. Fetch Note Detail -> file_url is shown!
    $response = $this->withHeaders($headers)->getJson(route('notes.show', $this->note->id));
    $response->assertStatus(200)
        ->assertJsonPath('data.is_locked', false)
        ->assertJsonPath('data.file_url', 'https://example.com/notes/quadratics.pdf');

    // 5. Fetch Test Detail -> questions are shown!
    $response = $this->withHeaders($headers)->getJson(route('tests.show', $this->test->id));
    $response->assertStatus(200)
        ->assertJsonPath('data.is_locked', false)
        ->assertJsonPath('data.has_access', true)
        ->assertJsonCount(1, 'data.questions');

    // 6. Attempt Test -> succeeds!
    $response = $this->withHeaders($headers)->postJson(route('tests.attempt', $this->test->id), [
        'answers' => [(string) $this->question->id => 'a'],
    ]);
    $response->assertStatus(200)
        ->assertJsonPath('data.is_passed', true)
        ->assertJsonPath('data.score', 10);

    // 7. Complete Chapter -> succeeds!
    $response = $this->withHeaders($headers)->postJson(route('chapters.complete', $this->chapter->id));
    $response->assertStatus(200)
        ->assertJsonPath('data.completed', true);
});
