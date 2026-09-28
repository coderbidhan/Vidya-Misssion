<?php

use App\Models\StudentProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

test('privacy policy web view page is accessible', function () {
    $response = $this->get(route('privacy-policy'));
    $response->assertStatus(200)
        ->assertSee('Privacy Policy')
        ->assertSee('VidyaMission');
});

test('terms and conditions web view page is accessible', function () {
    $response = $this->get(route('terms-and-conditions'));
    $response->assertStatus(200)
        ->assertSee('Terms and Conditions')
        ->assertSee('VidyaMission');
});

test('privacy policy api endpoint returns structured data', function () {
    $response = $this->getJson(route('api.privacy-policy'));
    $response->assertStatus(200)
        ->assertJsonPath('success', true)
        ->assertJsonStructure([
            'success',
            'message',
            'data' => [
                'title',
                'last_updated',
                'sections' => [
                    '*' => ['heading', 'content'],
                ],
            ],
        ]);
});

test('terms and conditions api endpoint returns structured data', function () {
    $response = $this->getJson(route('api.terms-and-conditions'));
    $response->assertStatus(200)
        ->assertJsonPath('success', true)
        ->assertJsonStructure([
            'success',
            'message',
            'data' => [
                'title',
                'last_updated',
                'sections' => [
                    '*' => ['heading', 'content'],
                ],
            ],
        ]);
});

test('account deletion is unauthorized for unauthenticated users', function () {
    $response = $this->deleteJson(route('profile.destroy'));
    $response->assertStatus(401);
});

test('authenticated student can delete their own account and purge records', function () {
    Storage::fake('public');

    // 1. Create a user and matching profile
    $user = User::create([
        'name' => 'Delete Me',
        'mobile' => '9999999999',
        'password' => bcrypt('password123'),
        'role' => 'student',
    ]);

    $profile = StudentProfile::create([
        'user_id' => $user->id,
        'name' => 'Delete Me',
        'class' => 10,
        'district' => 'Kolkata',
        'avatar_url' => Storage::disk('public')->url('avatars/temp.png'),
    ]);

    // Put a dummy file on the disk
    Storage::disk('public')->put('avatars/temp.png', 'fake image content');
    $this->assertTrue(Storage::disk('public')->exists('avatars/temp.png'));

    // Verify database has records
    $this->assertDatabaseHas('users', ['id' => $user->id]);
    $this->assertDatabaseHas('student_profiles', ['user_id' => $user->id]);

    // 2. Perform authenticated delete request
    $response = $this->actingAs($user, 'sanctum')
        ->deleteJson(route('profile.destroy'));

    // 3. Verify response
    $response->assertStatus(200)
        ->assertJsonPath('success', true)
        ->assertJsonPath('message', 'Your account and all associated data have been permanently deleted.');

    // 4. Verify user record is deleted
    $this->assertDatabaseMissing('users', ['id' => $user->id]);

    // 5. Verify cascade deletion removed student profile
    $this->assertDatabaseMissing('student_profiles', ['user_id' => $user->id]);

    // 6. Verify avatar file is deleted
    Storage::disk('public')->assertMissing('avatars/temp.png');
});
