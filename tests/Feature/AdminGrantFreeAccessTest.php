<?php

use App\Models\Payment;
use App\Models\Subscription;
use App\Models\User;
use App\Models\UserSubscription;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->admin = User::create([
        'name' => 'System Admin',
        'email' => 'admin@example.com',
        'mobile' => '9999999999',
        'role' => 'admin',
        'password' => bcrypt('secret123'),
        'is_active' => true,
        'is_verified' => true,
    ]);

    $this->student = User::create([
        'name' => 'Rahul Verma',
        'email' => 'rahul@example.com',
        'mobile' => '9888888888',
        'role' => 'student',
        'password' => bcrypt('secret123'),
        'is_active' => true,
        'is_verified' => true,
        'trial_starts_at' => now()->subDays(10),
        'trial_ends_at' => now()->subDays(3), // Expired trial
    ]);

    $this->plan = Subscription::create([
        'name' => 'Annual Gold Plan',
        'price' => 2999.00,
        'duration_days' => 365,
        'features' => ['all_videos', 'all_notes', 'all_tests'],
        'is_active' => true,
    ]);
});

it('allows admin to grant complimentary subscription access to a student', function () {
    expect($this->student->hasAccess())->toBeFalse();

    $response = $this->actingAs($this->admin, 'sanctum')
        ->postJson("/api/admin/students/{$this->student->id}/grant-access", [
            'access_type' => 'subscription',
            'subscription_id' => $this->plan->id,
            'duration_days' => 60,
            'reason' => 'Merit scholarship award',
        ]);

    $response->assertOk()
        ->assertJsonPath('success', true);

    $this->assertDatabaseHas('user_subscriptions', [
        'user_id' => $this->student->id,
        'subscription_id' => $this->plan->id,
        'payment_status' => 'completed',
        'payment_gateway' => 'admin_grant',
    ]);

    $this->assertDatabaseHas('payments', [
        'user_id' => $this->student->id,
        'amount' => 0,
        'status' => 'captured',
        'gateway' => 'admin_grant',
        'payment_method' => 'admin_complimentary',
    ]);

    $freshStudent = $this->student->fresh();
    expect($freshStudent->hasActiveSubscription())->toBeTrue();
    expect($freshStudent->hasAccess())->toBeTrue();
});

it('allows admin to extend trial access to a student', function () {
    expect($this->student->isInTrial())->toBeFalse();

    $response = $this->actingAs($this->admin, 'sanctum')
        ->postJson("/api/admin/students/{$this->student->id}/grant-access", [
            'access_type' => 'trial',
            'duration_days' => 14,
            'reason' => 'Customer support extension',
        ]);

    $response->assertOk()
        ->assertJsonPath('success', true);

    $freshStudent = $this->student->fresh();
    expect($freshStudent->isInTrial())->toBeTrue();
    expect($freshStudent->hasAccess())->toBeTrue();
    expect($freshStudent->trial_ends_at->isFuture())->toBeTrue();
});

it('extends from active subscription end date when granting a subsequent complimentary pass', function () {
    $existingEnd = now()->addDays(20);
    UserSubscription::create([
        'user_id' => $this->student->id,
        'subscription_id' => $this->plan->id,
        'payment_id' => 'admin_prev_123',
        'payment_gateway' => 'admin_grant',
        'payment_status' => 'completed',
        'starts_at' => now(),
        'ends_at' => $existingEnd,
    ]);

    $response = $this->actingAs($this->admin, 'sanctum')
        ->postJson("/api/admin/students/{$this->student->id}/grant-access", [
            'access_type' => 'subscription',
            'subscription_id' => $this->plan->id,
            'duration_days' => 30,
        ]);

    $response->assertOk();

    $latestSub = $this->student->fresh()->userSubscriptions()->orderByDesc('ends_at')->first();
    expect((int) round($existingEnd->diffInDays($latestSub->ends_at)))->toEqual(30);
});

it('prevents non-admins from granting free access', function () {
    $otherStudent = User::create([
        'name' => 'Other Student',
        'email' => 'other@example.com',
        'mobile' => '9777777777',
        'role' => 'student',
        'password' => bcrypt('secret123'),
        'is_active' => true,
        'is_verified' => true,
    ]);

    $response = $this->actingAs($otherStudent, 'sanctum')
        ->postJson("/api/admin/students/{$this->student->id}/grant-access", [
            'access_type' => 'trial',
            'duration_days' => 30,
        ]);

    $response->assertForbidden();
});

it('validates duration days and subscription requirements', function () {
    $response = $this->actingAs($this->admin, 'sanctum')
        ->postJson("/api/admin/students/{$this->student->id}/grant-access", [
            'access_type' => 'invalid_type',
            'duration_days' => 0,
        ]);

    $response->assertUnprocessable()
        ->assertJsonValidationErrors(['access_type', 'duration_days']);
});
