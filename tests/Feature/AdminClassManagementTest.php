<?php

namespace Tests\Feature;

use App\Models\AcademicClass;
use App\Models\Chapter;
use App\Models\ClassSubject;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class AdminClassManagementTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@test.com',
            'mobile' => '9999990001',
            'role' => 'admin',
            'password' => bcrypt('AdminSecret@123'),
            'is_active' => true,
            'is_verified' => true,
        ]);
    }

    public function test_admin_can_list_academic_classes_with_metrics(): void
    {
        Sanctum::actingAs($this->admin);

        $subject = Subject::create([
            'name' => 'Mathematics',
            'color' => '#6D4AFF',
            'icon' => '📐',
            'is_active' => true,
        ]);

        ClassSubject::create([
            'class_level' => 10,
            'subject_id' => $subject->id,
        ]);

        Chapter::create([
            'subject_id' => $subject->id,
            'class_level' => 10,
            'title' => 'Quadratic Equations',
            'sort_order' => 1,
            'is_active' => true,
        ]);

        $response = $this->getJson('/api/admin/classes');

        $response->assertOk()
            ->assertJsonPath('success', true);

        $classes = $response->json('data');
        $this->assertNotEmpty($classes);

        $cls10 = collect($classes)->firstWhere('class_level', 10);
        $this->assertNotNull($cls10);
        $this->assertEquals(1, $cls10['subjects_count']);
        $this->assertEquals(1, $cls10['chapters_count']);
    }

    public function test_admin_can_create_a_new_academic_class(): void
    {
        Sanctum::actingAs($this->admin);

        $payload = [
            'class_level' => 13,
            'name' => 'Class 13 Foundation',
            'display_name' => 'Class 13 (Repeater / Foundation)',
            'board' => 'Competitive',
            'stage' => 'Higher Secondary',
            'stream' => 'Science',
            'sort_order' => 13,
            'is_active' => true,
        ];

        $response = $this->postJson('/api/admin/classes', $payload);

        $response->assertCreated()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.class_level', 13)
            ->assertJsonPath('data.name', 'Class 13 Foundation');

        $this->assertDatabaseHas('academic_classes', [
            'class_level' => 13,
            'name' => 'Class 13 Foundation',
            'board' => 'Competitive',
        ]);
    }

    public function test_admin_cannot_create_duplicate_class_level(): void
    {
        Sanctum::actingAs($this->admin);

        // Class 10 was already seeded by migration
        $response = $this->postJson('/api/admin/classes', [
            'class_level' => 10,
            'name' => 'Duplicate Class 10',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['class_level']);
    }

    public function test_admin_can_update_an_academic_class(): void
    {
        Sanctum::actingAs($this->admin);

        // Class 12 was seeded by migration
        $class = AcademicClass::where('class_level', 12)->firstOrFail();

        $response = $this->putJson("/api/admin/classes/{$class->id}", [
            'name' => 'Class 12 (Senior Secondary)',
            'board' => 'WBCHSE',
            'stage' => 'Higher Secondary',
        ]);

        $response->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.name', 'Class 12 (Senior Secondary)');

        $this->assertDatabaseHas('academic_classes', [
            'id' => $class->id,
            'name' => 'Class 12 (Senior Secondary)',
        ]);
    }

    public function test_admin_can_toggle_class_status(): void
    {
        Sanctum::actingAs($this->admin);

        // Class 5 was seeded by migration
        $class = AcademicClass::where('class_level', 5)->firstOrFail();

        $response = $this->postJson("/api/admin/classes/{$class->id}/toggle-status");

        $response->assertOk()
            ->assertJsonPath('data.is_active', false);

        $this->assertFalse($class->fresh()->is_active);
    }

    public function test_admin_can_delete_class_without_linked_data(): void
    {
        Sanctum::actingAs($this->admin);

        $class = AcademicClass::create([
            'class_level' => 20,
            'name' => 'Class 20',
            'is_active' => true,
        ]);

        $response = $this->deleteJson("/api/admin/classes/{$class->id}");

        $response->assertOk();
        $this->assertDatabaseMissing('academic_classes', ['id' => $class->id]);
    }

    public function test_public_classes_endpoint_returns_active_classes(): void
    {
        $response = $this->getJson('/api/classes');

        $response->assertOk()
            ->assertJsonPath('success', true);

        $classes = $response->json('data');
        $this->assertTrue(collect($classes)->contains('class_level', 10));
    }
}
