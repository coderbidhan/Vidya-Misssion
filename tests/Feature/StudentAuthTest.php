<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

uses(RefreshDatabase::class);

test('student registration succeeds with valid data', function () {
    $payload = [
        'name' => 'John Doe',
        'district' => 'Paschim Bardhaman',
        'mobile' => '9876543210',
        'class' => 10,
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ];

    $response = $this->postJson(route('auth.register'), $payload);

    $response->assertStatus(201)
        ->assertJsonPath('success', true)
        ->assertJsonStructure([
            'success',
            'message',
            'data' => [
                'token',
                'user' => ['id', 'name', 'mobile', 'role', 'trial_starts_at', 'trial_ends_at'],
                'profile' => ['id', 'user_id', 'name', 'class', 'district'],
            ],
        ]);

    $this->assertDatabaseHas('users', [
        'mobile' => '9876543210',
        'role' => 'student',
    ]);

    $this->assertDatabaseHas('student_profiles', [
        'name' => 'John Doe',
        'district' => 'Paschim Bardhaman',
        'class' => 10,
    ]);
});

test('student registration fails with invalid data', function () {
    // 1. Missing name
    $response = $this->postJson(route('auth.register'), [
        'district' => 'Paschim Bardhaman',
        'mobile' => '9876543210',
        'class' => 10,
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ]);
    $response->assertStatus(422);

    // 2. Invalid class level (out of 1-12)
    $response = $this->postJson(route('auth.register'), [
        'name' => 'John Doe',
        'district' => 'Paschim Bardhaman',
        'mobile' => '9876543210',
        'class' => 15,
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ]);
    $response->assertStatus(422);

    // 3. Invalid Indian mobile format
    $response = $this->postJson(route('auth.register'), [
        'name' => 'John Doe',
        'district' => 'Paschim Bardhaman',
        'mobile' => '1234567890',
        'class' => 10,
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ]);
    $response->assertStatus(422);
});

test('student login succeeds with correct credentials', function () {
    $user = User::create([
        'name' => 'Jane Doe',
        'mobile' => '9876543211',
        'password' => Hash::make('password123'),
        'role' => 'student',
    ]);

    $response = $this->postJson(route('auth.login'), [
        'mobile' => '9876543211',
        'password' => 'password123',
    ]);

    $response->assertStatus(200)
        ->assertJsonPath('success', true)
        ->assertJsonStructure([
            'success',
            'message',
            'data' => [
                'token',
                'user',
            ],
        ]);
});

test('student login fails with incorrect credentials', function () {
    $user = User::create([
        'name' => 'Jane Doe',
        'mobile' => '9876543211',
        'password' => Hash::make('password123'),
        'role' => 'student',
    ]);

    $response = $this->postJson(route('auth.login'), [
        'mobile' => '9876543211',
        'password' => 'wrongpassword',
    ]);

    $response->assertStatus(401)
        ->assertJsonPath('success', false);
});

test('forgot password updates student password successfully', function () {
    $user = User::create([
        'name' => 'Jane Doe',
        'mobile' => '9876543211',
        'password' => Hash::make('password123'),
        'role' => 'student',
    ]);

    $response = $this->actingAs($user, 'sanctum')
        ->postJson(route('auth.forgot-password'), [
            'password' => 'newpassword123',
            'password_confirmation' => 'newpassword123',
        ]);

    $response->assertStatus(200)
        ->assertJsonPath('success', true);

    $this->assertTrue(Hash::check('newpassword123', $user->fresh()->password));
});
