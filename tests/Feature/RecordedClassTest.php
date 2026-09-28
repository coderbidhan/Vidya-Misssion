<?php

use App\Models\Chapter;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('admin can create recorded class with plain Google Drive ID', function () {
    $admin = User::create([
        'name' => 'Admin User',
        'email' => 'admin@vidyamission.com',
        'password' => bcrypt('password123'),
        'role' => 'admin',
    ]);

    $teacher = User::create([
        'name' => 'Dr. Sourav Sen',
        'mobile' => '9876543220',
        'password' => bcrypt('password123'),
        'role' => 'teacher',
    ]);

    $subject = Subject::create([
        'name' => 'Bengali',
        'color' => '#3B82F6',
        'icon' => 'book-default',
    ]);

    $chapter = Chapter::create([
        'subject_id' => $subject->id,
        'class_level' => 10,
        'title' => 'Gyananjon',
    ]);

    $payload = [
        'chapter_id' => $chapter->id,
        'teacher_id' => $teacher->id,
        'title' => 'Understanding Gyananjon',
        'gdrive_file_id' => '1BxiMVs0XRA5nFMdKvBdBZjgmUUqptlbs74',
    ];

    $response = $this->actingAs($admin, 'sanctum')
        ->postJson(route('admin.recorded-classes.store'), $payload);

    $response->assertStatus(201);

    $this->assertDatabaseHas('recorded_classes', [
        'chapter_id' => $chapter->id,
        'gdrive_file_id' => '1BxiMVs0XRA5nFMdKvBdBZjgmUUqptlbs74',
        'gdrive_embed_url' => 'https://drive.google.com/file/d/1BxiMVs0XRA5nFMdKvBdBZjgmUUqptlbs74/preview',
    ]);
});

test('admin can create recorded class with full Google Drive URL', function () {
    $admin = User::create(['name' => 'Admin', 'email' => 'admin@vidyamission.com', 'password' => bcrypt('password123'), 'role' => 'admin']);
    $teacher = User::create(['name' => 'Teacher', 'mobile' => '9876543220', 'password' => bcrypt('password123'), 'role' => 'teacher']);
    $subject = Subject::create(['name' => 'Math', 'color' => '#3B82F6', 'icon' => 'book-default']);
    $chapter = Chapter::create(['subject_id' => $subject->id, 'class_level' => 10, 'title' => 'Equations']);

    $payload = [
        'chapter_id' => $chapter->id,
        'teacher_id' => $teacher->id,
        'title' => 'Math Session',
        'gdrive_file_id' => 'https://drive.google.com/file/d/1A2B3C4D5E6F/view?usp=sharing',
    ];

    $response = $this->actingAs($admin, 'sanctum')
        ->postJson(route('admin.recorded-classes.store'), $payload);

    $response->assertStatus(201);

    $this->assertDatabaseHas('recorded_classes', [
        'gdrive_file_id' => '1A2B3C4D5E6F',
        'gdrive_embed_url' => 'https://drive.google.com/file/d/1A2B3C4D5E6F/preview',
    ]);
});

test('admin can create recorded class with full YouTube video URL', function () {
    $admin = User::create(['name' => 'Admin', 'email' => 'admin@vidyamission.com', 'password' => bcrypt('password123'), 'role' => 'admin']);
    $teacher = User::create(['name' => 'Teacher', 'mobile' => '9876543220', 'password' => bcrypt('password123'), 'role' => 'teacher']);
    $subject = Subject::create(['name' => 'Math', 'color' => '#3B82F6', 'icon' => 'book-default']);
    $chapter = Chapter::create(['subject_id' => $subject->id, 'class_level' => 10, 'title' => 'Equations']);

    $payload = [
        'chapter_id' => $chapter->id,
        'teacher_id' => $teacher->id,
        'title' => 'Math Session 2',
        'gdrive_file_id' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
    ];

    $response = $this->actingAs($admin, 'sanctum')
        ->postJson(route('admin.recorded-classes.store'), $payload);

    $response->assertStatus(201);

    $this->assertDatabaseHas('recorded_classes', [
        'gdrive_file_id' => 'dQw4w9WgXcQ',
        'gdrive_embed_url' => 'https://www.youtube.com/embed/dQw4w9WgXcQ',
    ]);
});

test('admin can create recorded class with plain 11-char YouTube video ID', function () {
    $admin = User::create(['name' => 'Admin', 'email' => 'admin@vidyamission.com', 'password' => bcrypt('password123'), 'role' => 'admin']);
    $teacher = User::create(['name' => 'Teacher', 'mobile' => '9876543220', 'password' => bcrypt('password123'), 'role' => 'teacher']);
    $subject = Subject::create(['name' => 'Math', 'color' => '#3B82F6', 'icon' => 'book-default']);
    $chapter = Chapter::create(['subject_id' => $subject->id, 'class_level' => 10, 'title' => 'Equations']);

    $payload = [
        'chapter_id' => $chapter->id,
        'teacher_id' => $teacher->id,
        'title' => 'Math Session 3',
        'gdrive_file_id' => 'y881t8ilMyc',
    ];

    $response = $this->actingAs($admin, 'sanctum')
        ->postJson(route('admin.recorded-classes.store'), $payload);

    $response->assertStatus(201);

    $this->assertDatabaseHas('recorded_classes', [
        'gdrive_file_id' => 'y881t8ilMyc',
        'gdrive_embed_url' => 'https://www.youtube.com/embed/y881t8ilMyc',
    ]);
});
