<?php

use App\Models\User;
use App\Services\DeviceSessionService;
use App\Services\OtpService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::create([
        'name' => 'Aditi Sharma',
        'email' => 'aditi@example.com',
        'mobile' => '9876543210',
        'password' => Hash::make('password123'),
        'role' => 'student',
        'is_verified' => true,
        'is_active' => true,
        'trial_starts_at' => now(),
        'trial_ends_at' => now()->addDays(7),
    ]);
});

/*
|--------------------------------------------------------------------------
| Test 1: First Login
|--------------------------------------------------------------------------
*/
test('first login on device A succeeds and can access protected endpoints', function () {
    $loginResponse = $this->postJson(route('auth.login'), [
        'mobile' => '9876543210',
        'password' => 'password123',
    ]);

    $loginResponse->assertStatus(200)
        ->assertJsonPath('success', true);

    $tokenA = $loginResponse->json('data.token');
    expect($tokenA)->not->toBeEmpty();

    // Device A calls protected profile endpoint
    $profileResponse = $this->withHeader('Authorization', 'Bearer '.$tokenA)
        ->getJson(route('profile.show'));

    $profileResponse->assertStatus(200)
        ->assertJsonPath('success', true)
        ->assertJsonPath('data.user.id', $this->user->id);
});

/*
|--------------------------------------------------------------------------
| Test 2: Second-Device Login (Core Single-Device Requirement)
|--------------------------------------------------------------------------
*/
test('login on device B supersedes device A and device A receives SESSION_REPLACED', function () {
    // 1. Device A logs in
    $responseA = $this->postJson(route('auth.login'), [
        'mobile' => '9876543210',
        'password' => 'password123',
    ]);
    $responseA->assertStatus(200);
    $tokenA = $responseA->json('data.token');

    // Confirm Device A works initially
    $this->withHeader('Authorization', 'Bearer '.$tokenA)
        ->getJson(route('profile.show'))
        ->assertStatus(200);

    // 2. Device B logs into the same account
    $responseB = $this->postJson(route('auth.login'), [
        'mobile' => '9876543210',
        'password' => 'password123',
    ]);
    $responseB->assertStatus(200);
    $tokenB = $responseB->json('data.token');
    expect($tokenB)->not->toEqual($tokenA);

    // 3. Confirm Device B can access protected endpoints
    $this->withHeader('Authorization', 'Bearer '.$tokenB)
        ->getJson(route('profile.show'))
        ->assertStatus(200)
        ->assertJsonPath('data.user.id', $this->user->id);

    // 4. Confirm Device A is rejected with HTTP 401 and code SESSION_REPLACED
    $staleResponseA = $this->withHeader('Authorization', 'Bearer '.$tokenA)
        ->getJson(route('profile.show'));

    $staleResponseA->assertStatus(401)
        ->assertJson([
            'success' => false,
            'message' => 'Your account has been logged in on another device. Please log in again.',
            'code' => 'SESSION_REPLACED',
        ]);

    // 5. Repeated requests from Device A also consistently receive SESSION_REPLACED
    $staleResponseA2 = $this->withHeader('Authorization', 'Bearer '.$tokenA)
        ->getJson(route('profile.show'));

    $staleResponseA2->assertStatus(401)
        ->assertJsonPath('code', 'SESSION_REPLACED');
});

/*
|--------------------------------------------------------------------------
| Test 3: Different Users Do Not Interfere
|--------------------------------------------------------------------------
*/
test('different users remain independently authenticated on separate devices', function () {
    $user2 = User::create([
        'name' => 'Rahul Verma',
        'email' => 'rahul@example.com',
        'mobile' => '9876543211',
        'password' => Hash::make('password123'),
        'role' => 'student',
        'is_verified' => true,
        'is_active' => true,
        'trial_starts_at' => now(),
        'trial_ends_at' => now()->addDays(7),
    ]);

    // User A logs in on Device A
    $respA = $this->postJson(route('auth.login'), [
        'mobile' => '9876543210',
        'password' => 'password123',
    ]);
    $tokenA = $respA->json('data.token');

    // User B logs in on Device B
    $respB = $this->postJson(route('auth.login'), [
        'mobile' => '9876543211',
        'password' => 'password123',
    ]);
    $tokenB = $respB->json('data.token');

    // Both remain active and authenticated
    $this->withHeader('Authorization', 'Bearer '.$tokenA)
        ->getJson(route('profile.show'))
        ->assertStatus(200)
        ->assertJsonPath('data.user.name', 'Aditi Sharma');

    $this->withHeader('Authorization', 'Bearer '.$tokenB)
        ->getJson(route('profile.show'))
        ->assertStatus(200)
        ->assertJsonPath('data.user.name', 'Rahul Verma');
});

/*
|--------------------------------------------------------------------------
| Test 4: Invalid Credentials Preserve Existing Active Session
|--------------------------------------------------------------------------
*/
test('failed login attempt does not invalidate existing legitimate device session', function () {
    // 1. Establish valid session on Device A
    $respA = $this->postJson(route('auth.login'), [
        'mobile' => '9876543210',
        'password' => 'password123',
    ]);
    $tokenA = $respA->json('data.token');

    // 2. Attempt login on Device B with incorrect password
    $failedResp = $this->postJson(route('auth.login'), [
        'mobile' => '9876543210',
        'password' => 'wrongpassword',
    ]);
    $failedResp->assertStatus(401)
        ->assertJsonPath('code', 'INVALID_CREDENTIALS');

    // 3. Confirm Device A is still fully active
    $this->withHeader('Authorization', 'Bearer '.$tokenA)
        ->getJson(route('profile.show'))
        ->assertStatus(200)
        ->assertJsonPath('data.user.id', $this->user->id);
});

/*
|--------------------------------------------------------------------------
| Test 5: Concurrency Safety and Session Replacement Integrity
|--------------------------------------------------------------------------
*/
test('consecutive and concurrent logins leave exactly one winning active session', function () {
    $service = app(DeviceSessionService::class);

    // Simulate two sequential / concurrent logins for the same user
    $token1 = $service->createDeviceSession($this->user, 'api-token', now()->addDays(30));
    $token2 = $service->createDeviceSession($this->user, 'api-token', now()->addDays(30));

    // The winning token must be token 2
    expect($this->user->fresh()->current_device_token_id)->toEqual($token2->accessToken->id);

    // Token 1 must be detected as superseded
    expect($service->isTokenSuperseded($token1->accessToken->id))->toBeTrue();
    expect($service->isTokenActive($this->user->fresh(), $token1->accessToken->id))->toBeFalse();

    // Token 2 must be active
    expect($service->isTokenActive($this->user->fresh(), $token2->accessToken->id))->toBeTrue();

    // Token 2 accesses protected API successfully
    $this->withHeader('Authorization', 'Bearer '.$token2->plainTextToken)
        ->getJson(route('profile.show'))
        ->assertStatus(200);

    // Token 1 is rejected with SESSION_REPLACED
    $this->withHeader('Authorization', 'Bearer '.$token1->plainTextToken)
        ->getJson(route('profile.show'))
        ->assertStatus(401)
        ->assertJsonPath('code', 'SESSION_REPLACED');
});

/*
|--------------------------------------------------------------------------
| Test 6: Normal Logout Invalidation
|--------------------------------------------------------------------------
*/
test('normal logout invalidates the device session completely', function () {
    $resp = $this->postJson(route('auth.login'), [
        'mobile' => '9876543210',
        'password' => 'password123',
    ]);
    $token = $resp->json('data.token');

    // Logout
    $this->withHeader('Authorization', 'Bearer '.$token)
        ->postJson(route('auth.logout'))
        ->assertStatus(200)
        ->assertJsonPath('success', true);

    // Subsequent call must fail
    $this->withHeader('Authorization', 'Bearer '.$token)
        ->getJson(route('profile.show'))
        ->assertStatus(401);

    expect($this->user->fresh()->current_device_token_id)->toBeNull();
});

/*
|--------------------------------------------------------------------------
| Test 7: Token Refresh Flow
|--------------------------------------------------------------------------
*/
test('refresh token issues new active token and invalidates old token', function () {
    $resp = $this->postJson(route('auth.login'), [
        'mobile' => '9876543210',
        'password' => 'password123',
    ]);
    $tokenA = $resp->json('data.token');

    // Refresh token
    $refreshResp = $this->withHeader('Authorization', 'Bearer '.$tokenA)
        ->postJson(route('auth.refresh-token'));

    $refreshResp->assertStatus(200)
        ->assertJsonPath('success', true);

    $tokenRefreshed = $refreshResp->json('data.token');
    expect($tokenRefreshed)->not->toEqual($tokenA);

    // New token works
    $this->withHeader('Authorization', 'Bearer '.$tokenRefreshed)
        ->getJson(route('profile.show'))
        ->assertStatus(200);

    // Old token is superseded
    $this->withHeader('Authorization', 'Bearer '.$tokenA)
        ->getJson(route('profile.show'))
        ->assertStatus(401)
        ->assertJsonPath('code', 'SESSION_REPLACED');
});

/*
|--------------------------------------------------------------------------
| Test 8: OTP Login Single-Device Enforcement
|--------------------------------------------------------------------------
*/
test('otp login on device B supersedes previous password login on device A', function () {
    // 1. Password login on Device A
    $respA = $this->postJson(route('auth.login'), [
        'mobile' => '9876543210',
        'password' => 'password123',
    ]);
    $tokenA = $respA->json('data.token');

    // 2. Mock OTP verification on Device B
    $this->mock(OtpService::class, function ($mock) {
        $mock->shouldReceive('verifyOtp')->once()->andReturn(true);
    });

    $respB = $this->postJson(route('auth.verify-otp'), [
        'email' => 'aditi@example.com',
        'otp' => '123456',
    ]);
    $respB->assertStatus(200);
    $tokenB = $respB->json('data.token');

    // Device B works
    $this->withHeader('Authorization', 'Bearer '.$tokenB)
        ->getJson(route('profile.show'))
        ->assertStatus(200);

    // Device A is rejected with SESSION_REPLACED
    $this->withHeader('Authorization', 'Bearer '.$tokenA)
        ->getJson(route('profile.show'))
        ->assertStatus(401)
        ->assertJsonPath('code', 'SESSION_REPLACED');
});

/*
|--------------------------------------------------------------------------
| Test 9: Admin Single-Device Session Enforcement
|--------------------------------------------------------------------------
*/
test('admin login on device B supersedes device A admin session', function () {
    $admin = User::create([
        'name' => 'Super Admin',
        'email' => 'admin@example.com',
        'password' => Hash::make('AdminSecret123!'),
        'role' => 'admin',
        'is_verified' => true,
        'is_active' => true,
    ]);

    // Admin logs in on Browser A
    $respA = $this->post(route('admin.login.submit'), [
        'email' => 'admin@example.com',
        'password' => 'AdminSecret123!',
    ]);
    $respA->assertRedirect(route('admin.dashboard'));
    $tokenA = session('admin_token');

    // Token A works for admin API
    $this->withHeader('Authorization', 'Bearer '.$tokenA)
        ->getJson(route('admin.admins'))
        ->assertStatus(200);

    // Admin logs in on Browser B
    $respB = $this->post(route('admin.login.submit'), [
        'email' => 'admin@example.com',
        'password' => 'AdminSecret123!',
    ]);
    $respB->assertRedirect(route('admin.dashboard'));
    $tokenB = session('admin_token');
    expect($tokenB)->not->toEqual($tokenA);

    // Token B works
    $this->withHeader('Authorization', 'Bearer '.$tokenB)
        ->getJson(route('admin.admins'))
        ->assertStatus(200);

    // Token A is rejected with SESSION_REPLACED
    $this->withHeader('Authorization', 'Bearer '.$tokenA)
        ->getJson(route('admin.admins'))
        ->assertStatus(401)
        ->assertJsonPath('code', 'SESSION_REPLACED');
});

/*
|--------------------------------------------------------------------------
| Test 10: Admin Revoke Session Endpoint
|--------------------------------------------------------------------------
*/
test('admin can revoke active session of a user', function () {
    $admin = User::create([
        'name' => 'Admin Officer',
        'email' => 'officer@example.com',
        'password' => Hash::make('AdminSecret123!'),
        'role' => 'admin',
        'is_verified' => true,
        'is_active' => true,
    ]);

    // Student logs in
    $respStudent = $this->postJson(route('auth.login'), [
        'mobile' => '9876543210',
        'password' => 'password123',
    ]);
    $studentToken = $respStudent->json('data.token');

    // Admin revokes student's session
    $this->actingAs($admin, 'sanctum')
        ->postJson(route('admin.users.revoke-session', ['id' => $this->user->id]))
        ->assertStatus(200)
        ->assertJsonPath('success', true);

    // Student is now rejected
    $this->withHeader('Authorization', 'Bearer '.$studentToken)
        ->getJson(route('profile.show'))
        ->assertStatus(401)
        ->assertJsonPath('code', 'SESSION_REPLACED');
});

/*
|--------------------------------------------------------------------------
| Test 11: Public Endpoints Accessible Without Authentication
|--------------------------------------------------------------------------
*/
test('public endpoints remain accessible and unaffected by single-device middleware', function () {
    $this->getJson(route('classes.index'))
        ->assertStatus(200);

    $this->getJson(route('docs.json'))
        ->assertStatus(200);
});

/*
|--------------------------------------------------------------------------
| Test 12: Invalid or Bogus Token Returns Standard Unauthenticated
|--------------------------------------------------------------------------
*/
test('bogus non-existent token returns standard Unauthenticated response instead of SESSION_REPLACED', function () {
    $this->withHeader('Authorization', 'Bearer 99999|fakeTokenSecret123')
        ->getJson(route('profile.show'))
        ->assertStatus(401)
        ->assertJson([
            'success' => false,
            'message' => 'Unauthenticated.',
        ])
        ->assertJsonMissing(['code' => 'SESSION_REPLACED']);
});
