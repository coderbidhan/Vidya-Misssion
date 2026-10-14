<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminWebViewsTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::create([
            'name' => 'Web Admin',
            'email' => 'webadmin@test.com',
            'mobile' => '9999990099',
            'role' => 'admin',
            'password' => bcrypt('password123'),
            'is_active' => true,
            'is_verified' => true,
        ]);
    }

    public function test_authenticated_admin_can_access_classes_view(): void
    {
        $response = $this->withSession([
            'admin_token' => 'mock-token',
            'admin_user' => [
                'id' => $this->admin->id,
                'name' => $this->admin->name,
                'email' => $this->admin->email,
                'role' => 'admin',
            ],
        ])->get('/admin/classes');

        $response->assertOk()
            ->assertSee('Class Management')
            ->assertSee('All Academic Classes');
    }

    public function test_authenticated_admin_can_access_students_view(): void
    {
        $response = $this->withSession([
            'admin_token' => 'mock-token',
            'admin_user' => [
                'id' => $this->admin->id,
                'name' => $this->admin->name,
                'email' => $this->admin->email,
                'role' => 'admin',
            ],
        ])->get('/admin/students');

        $response->assertOk()
            ->assertSee('Students')
            ->assertSee('Account Password')
            ->assertSee('Add Student');
    }

    public function test_authenticated_admin_can_access_subjects_view_with_class_management(): void
    {
        $response = $this->withSession([
            'admin_token' => 'mock-token',
            'admin_user' => [
                'id' => $this->admin->id,
                'name' => $this->admin->name,
                'email' => $this->admin->email,
                'role' => 'admin',
            ],
        ])->get('/admin/subjects');

        $response->assertOk()
            ->assertSee('Classes & Subjects')
            ->assertSee('quickAddClassModal');
    }

    public function test_home_page_displays_app_download_section_with_playstore_link(): void
    {
        $response = $this->get('/');

        $response->assertOk()
            ->assertSee('id="download-app"', false)
            ->assertSee('https://play.google.com/store/apps/details?id=com.vidyaMission.vidyaMission&pcampaignid=web_share', false)
            ->assertSee('Official Android App')
            ->assertSee('VidyaMission App')
            ->assertSee('Google Play')
            ->assertSee('Scan to Install');
    }
}
