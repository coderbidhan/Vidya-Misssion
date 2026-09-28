<?php

use App\Models\Chapter;
use App\Models\Note;
use App\Models\RecordedClass;
use App\Models\Subject;
use App\Models\Subscription;
use App\Models\User;
use App\Models\UserSubscription;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('subscribed student can access all premium content during subscription validity even after trial ends', function () {
    // 1. Create a student whose trial has expired
    $student = User::create([
        'name' => 'Subscribed Student',
        'mobile' => '9876543210',
        'email' => 'student@test.com',
        'password' => bcrypt('password123'),
        'role' => 'student',
        'is_verified' => true,
        'is_active' => true,
        'trial_starts_at' => now()->subDays(10),
        'trial_ends_at' => now()->subDays(7), // trial expired 7 days ago
    ]);

    $student->studentProfile()->create([
        'name' => 'Subscribed Student',
        'class' => '9',
    ]);

    // 2. Add an active subscription valid for 30 days
    $plan = Subscription::create([
        'name' => 'Monthly Plan',
        'price' => 199.00,
        'duration_days' => 30,
        'is_active' => true,
    ]);

    $userSub = UserSubscription::create([
        'user_id' => $student->id,
        'subscription_id' => $plan->id,
        'payment_gateway' => 'razorpay',
        'payment_id' => 'pay_123456',
        'payment_status' => 'completed',
        'starts_at' => now(),
        'ends_at' => now()->addDays(30),
    ]);

    expect($student->hasActiveSubscription())->toBeTrue();
    expect($student->hasContentAccess())->toBeTrue();
    expect($userSub->isActive())->toBeTrue();
    expect($userSub->daysRemaining())->toBeGreaterThan(0);

    // 3. Create content (subject, chapter, recorded class, note)
    $teacher = User::create([
        'name' => 'Teacher One',
        'email' => 'teacher@test.com',
        'password' => bcrypt('password123'),
        'role' => 'teacher',
    ]);

    $subject = Subject::create([
        'name' => 'Physical Science',
        'color' => '#10B981',
        'icon' => 'atom',
    ]);

    $chapter = Chapter::create([
        'subject_id' => $subject->id,
        'class_level' => 9,
        'title' => 'Matter and Structure',
    ]);

    $recordedClass = RecordedClass::create([
        'chapter_id' => $chapter->id,
        'teacher_id' => $teacher->id,
        'title' => 'Lecture 1: Atomic Theory',
        'gdrive_file_id' => 'abc123xyz',
        'gdrive_embed_url' => 'https://drive.google.com/file/d/abc123xyz/preview',
    ]);

    $note = Note::create([
        'chapter_id' => $chapter->id,
        'teacher_id' => $teacher->id,
        'title' => 'Atomic Structure PDF',
        'file_url' => 'https://example.com/notes.pdf',
        'file_type' => 'pdf',
    ]);

    // 4. Access content endpoints with Bearer token
    $resRecorded = $this->actingAs($student, 'sanctum')
        ->getJson(route('recorded-classes.by-chapter', ['id' => $chapter->id]));
    $resRecorded->assertStatus(200);

    $resNote = $this->actingAs($student, 'sanctum')
        ->getJson(route('notes.by-chapter', ['id' => $chapter->id]));
    $resNote->assertStatus(200);

    $resSubject = $this->actingAs($student, 'sanctum')
        ->getJson(route('subjects.index'));
    $resSubject->assertStatus(200);
    $resSubject->assertJsonPath('data.is_subscription_active', true);
});

test('expired subscription without trial blocks access with 403 TRIAL_EXPIRED and subscription_required', function () {
    $student = User::create([
        'name' => 'Expired Student',
        'mobile' => '9876543211',
        'email' => 'expired@test.com',
        'password' => bcrypt('password123'),
        'role' => 'student',
        'is_verified' => true,
        'is_active' => true,
        'trial_starts_at' => now()->subDays(40),
        'trial_ends_at' => now()->subDays(37),
    ]);

    $student->studentProfile()->create([
        'name' => 'Expired Student',
        'class' => '9',
    ]);

    // Expired subscription
    $plan = Subscription::create([
        'name' => 'Monthly Plan',
        'price' => 199.00,
        'duration_days' => 30,
        'is_active' => true,
    ]);

    UserSubscription::create([
        'user_id' => $student->id,
        'subscription_id' => $plan->id,
        'payment_gateway' => 'razorpay',
        'payment_id' => 'pay_old',
        'payment_status' => 'completed',
        'starts_at' => now()->subDays(35),
        'ends_at' => now()->subDays(5),
    ]);

    expect($student->hasActiveSubscription())->toBeFalse();
    expect($student->hasContentAccess())->toBeFalse();

    $subject = Subject::create(['name' => 'History', 'color' => '#F59E0B', 'icon' => 'landmark']);
    $chapter = Chapter::create(['subject_id' => $subject->id, 'class_level' => 9, 'title' => 'Ancient Bengal']);

    $res = $this->actingAs($student, 'sanctum')
        ->getJson(route('recorded-classes.by-chapter', ['id' => $chapter->id]));

    $res->assertStatus(403);
    $res->assertJson([
        'success' => false,
        'code' => 'TRIAL_EXPIRED',
        'subscription_required' => true,
    ]);
});
