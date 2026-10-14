<?php

namespace Tests\Feature;

use App\Models\StudentProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class StudentPasswordManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_student_registration_saves_encrypted_plain_password(): void
    {
        $payload = [
            'name' => 'Rahul Sharma',
            'mobile' => '9876543210',
            'password' => 'Student@1234',
            'password_confirmation' => 'Student@1234',
            'class' => 10,
            'school_name' => 'Kolkata High School',
            'district' => 'Kolkata',
        ];

        $response = $this->postJson('/api/auth/register', $payload);

        $response->assertCreated()
            ->assertJsonPath('success', true);

        $user = User::where('mobile', '9876543210')->first();
        $this->assertNotNull($user);
        $this->assertEquals('Student@1234', $user->plain_password);
    }

    public function test_student_forgot_password_updates_plain_password(): void
    {
        $student = User::create([
            'name' => 'Student Forgot Pass',
            'role' => 'student',
            'mobile' => '9876543211',
            'password' => bcrypt('OldPass@123'),
            'plain_password' => 'OldPass@123',
            'is_active' => true,
            'is_verified' => true,
        ]);

        Sanctum::actingAs($student);

        $response = $this->postJson('/api/auth/forgot-password', [
            'password' => 'NewSecret@999',
            'password_confirmation' => 'NewSecret@999',
        ]);

        $response->assertOk();
        $this->assertEquals('NewSecret@999', $student->fresh()->plain_password);
    }

    public function test_admin_can_view_student_account_password(): void
    {
        $admin = User::create([
            'name' => 'Admin Boss',
            'role' => 'admin',
            'email' => 'admin2@test.com',
            'mobile' => '9999990002',
            'password' => bcrypt('secret123'),
            'is_active' => true,
            'is_verified' => true,
        ]);

        $student = User::create([
            'name' => 'Priya Das',
            'role' => 'student',
            'mobile' => '9876543212',
            'password' => bcrypt('PriyaSecret!456'),
            'plain_password' => 'PriyaSecret!456',
            'is_active' => true,
            'is_verified' => true,
        ]);

        StudentProfile::create([
            'user_id' => $student->id,
            'name' => 'Priya Das',
            'class' => 9,
            'district' => 'Kolkata',
        ]);

        Sanctum::actingAs($admin);

        $response = $this->getJson('/api/admin/students');

        $response->assertOk()
            ->assertJsonPath('success', true);

        $students = $response->json('data');
        $priya = collect($students)->firstWhere('id', $student->id);

        $this->assertNotNull($priya);
        $this->assertEquals('PriyaSecret!456', $priya['account_password']);
    }

    public function test_admin_can_reset_student_password(): void
    {
        $admin = User::create([
            'name' => 'Admin Boss',
            'role' => 'admin',
            'email' => 'admin3@test.com',
            'mobile' => '9999990003',
            'password' => bcrypt('secret123'),
            'is_active' => true,
            'is_verified' => true,
        ]);

        $student = User::create([
            'name' => 'Reset Student',
            'role' => 'student',
            'mobile' => '9876543213',
            'password' => bcrypt('OldStudentPass'),
            'plain_password' => 'OldStudentPass',
            'is_active' => true,
            'is_verified' => true,
        ]);

        Sanctum::actingAs($admin);

        $response = $this->postJson("/api/admin/students/{$student->id}/password", [
            'password' => 'AdminAssigned@2026',
        ]);

        $response->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.account_password', 'AdminAssigned@2026');

        $this->assertEquals('AdminAssigned@2026', $student->fresh()->plain_password);
    }

    public function test_admin_can_create_student_with_password(): void
    {
        $admin = User::create([
            'name' => 'Admin Boss',
            'role' => 'admin',
            'email' => 'admin4@test.com',
            'mobile' => '9999990004',
            'password' => bcrypt('secret123'),
            'is_active' => true,
            'is_verified' => true,
        ]);

        Sanctum::actingAs($admin);

        $response = $this->postJson('/api/admin/students', [
            'name' => 'Amit Sen',
            'mobile' => '9876543214',
            'password' => 'AmitPass@2026',
            'class' => 11,
            'district' => 'Howrah',
        ]);

        $response->assertCreated()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.account_password', 'AmitPass@2026');

        $student = User::where('mobile', '9876543214')->first();
        $this->assertNotNull($student);
        $this->assertEquals('AmitPass@2026', $student->plain_password);
    }

    public function test_student_profile_api_does_not_leak_password(): void
    {
        $student = User::create([
            'name' => 'Sneha Roy',
            'role' => 'student',
            'mobile' => '9876543215',
            'password' => bcrypt('ConfidentialPass'),
            'plain_password' => 'ConfidentialPass',
            'is_active' => true,
            'is_verified' => true,
        ]);

        StudentProfile::create([
            'user_id' => $student->id,
            'name' => 'Sneha Roy',
            'class' => 8,
            'district' => 'Kolkata',
        ]);

        Sanctum::actingAs($student);

        $response = $this->getJson('/api/profile');

        $response->assertOk();
        $userData = $response->json('data.user');

        $this->assertArrayNotHasKey('plain_password', $userData);
        $this->assertArrayNotHasKey('password', $userData);
        $this->assertArrayNotHasKey('account_password', $userData);
    }
}
