<?php

use App\Models\Chapter;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('admin can create recorded class with youtube url', function () {
    $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);
    $teacher = User::factory()->create(['role' => 'teacher', 'is_active' => true]);
    $subject = Subject::create(['name' => 'Test Subject', 'color' => '#123456', 'icon' => 'test']);
    $chapter = Chapter::create([
        'subject_id' => $subject->id,
        'title' => 'Test Chapter',
        'is_active' => true,
    ]);

    $response = $this->actingAs($admin)
        ->postJson(route('admin.recorded-classes.store'), [
            'chapter_id' => $chapter->id,
            'teacher_id' => $teacher->id,
            'title' => 'YouTube Class Test',
            'video_type' => 'youtube',
            'video_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
            'duration_seconds' => 120,
        ]);

    $response->assertStatus(201);

    $this->assertDatabaseHas('recorded_classes', [
        'title' => 'YouTube Class Test',
        'video_type' => 'youtube',
        'gdrive_file_id' => 'dQw4w9WgXcQ',
        'gdrive_embed_url' => 'https://www.youtube.com/embed/dQw4w9WgXcQ',
    ]);
});

test('admin can create recorded class with google drive url', function () {
    $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);
    $teacher = User::factory()->create(['role' => 'teacher', 'is_active' => true]);
    $subject = Subject::create(['name' => 'Test Subject', 'color' => '#123456', 'icon' => 'test']);
    $chapter = Chapter::create([
        'subject_id' => $subject->id,
        'title' => 'Test Chapter',
        'is_active' => true,
    ]);

    $response = $this->actingAs($admin)
        ->postJson(route('admin.recorded-classes.store'), [
            'chapter_id' => $chapter->id,
            'teacher_id' => $teacher->id,
            'title' => 'Google Drive Class Test',
            'video_type' => 'google_drive',
            'video_url' => 'https://drive.google.com/file/d/1BxiMVs0XRA5nFMdKvBdBZjgmUUqptlbs74/view',
            'duration_seconds' => 300,
        ]);

    $response->assertStatus(201);

    $this->assertDatabaseHas('recorded_classes', [
        'title' => 'Google Drive Class Test',
        'video_type' => 'google_drive',
        'gdrive_file_id' => '1BxiMVs0XRA5nFMdKvBdBZjgmUUqptlbs74',
        'gdrive_embed_url' => 'https://drive.google.com/file/d/1BxiMVs0XRA5nFMdKvBdBZjgmUUqptlbs74/preview',
    ]);
});
