<?php

use App\Models\Payment;
use App\Models\Refund;
use App\Models\Subscription;
use App\Models\User;
use App\Models\UserSubscription;
use App\Services\Payment\RazorpayGateway;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;

uses(RefreshDatabase::class);

beforeEach(function () {
    // 1. Admin user
    $this->admin = User::create([
        'name' => 'Admin Boss',
        'email' => 'admin@vidyamission.com',
        'mobile' => '9999988888',
        'role' => 'admin',
        'password' => bcrypt('password123'),
        'is_active' => true,
        'is_verified' => true,
    ]);

    $this->adminToken = $this->admin->createToken('admin-token')->plainTextToken;
    $this->adminHeaders = ['Authorization' => "Bearer {$this->adminToken}"];

    // 2. Student user
    $this->student = User::create([
        'name' => 'Pooja Hegde',
        'email' => 'pooja@example.com',
        'mobile' => '9888877777',
        'role' => 'student',
        'password' => bcrypt('password123'),
        'is_active' => true,
        'is_verified' => true,
    ]);

    $this->studentToken = $this->student->createToken('student-token')->plainTextToken;
    $this->studentHeaders = ['Authorization' => "Bearer {$this->studentToken}"];

    // 3. Plan
    $this->plan = Subscription::create([
        'name' => 'Half-Yearly Pass',
        'price' => 2000.00,
        'duration_days' => 180,
        'is_active' => true,
    ]);

    // 4. Mock Gateway
    $this->mockGateway = Mockery::mock(RazorpayGateway::class, [
        config('vidyamission.razorpay.key_id'),
        config('vidyamission.razorpay.key_secret'),
        config('vidyamission.razorpay.webhook_secret'),
    ])->makePartial();

    $this->app->instance(RazorpayGateway::class, $this->mockGateway);
});

test('authorized admin can issue full refund for captured payment', function () {
    $payment = Payment::create([
        'user_id' => $this->student->id,
        'gateway_order_id' => 'order_rfnd_full_01',
        'gateway_payment_id' => 'pay_rfnd_full_01',
        'amount' => 200000, // ₹2000.00
        'currency' => 'INR',
        'status' => 'captured',
        'is_captured' => true,
    ]);

    $userSub = UserSubscription::create([
        'user_id' => $this->student->id,
        'subscription_id' => $this->plan->id,
        'gateway_order_id' => 'order_rfnd_full_01',
        'payment_id' => 'pay_rfnd_full_01',
        'payment_status' => 'completed',
        'payment_record_id' => $payment->id,
    ]);

    $payment->update([
        'payable_type' => UserSubscription::class,
        'payable_id' => $userSub->id,
    ]);

    $this->mockGateway->shouldReceive('createRefund')
        ->once()
        ->with('pay_rfnd_full_01', 200000, Mockery::any())
        ->andReturn([
            'id' => 'rfnd_full_gateway_01',
            'amount' => 200000,
            'status' => 'processed',
        ]);

    $response = $this->withHeaders($this->adminHeaders)
        ->postJson(route('admin.payments.refund', $payment->id), [
            'amount' => 2000.00,
            'reason' => 'Customer dissatisfaction within 24h',
        ]);

    $response->assertStatus(200)
        ->assertJsonPath('success', true)
        ->assertJsonPath('data.refund.status', 'processed');

    $freshPayment = $payment->fresh();
    expect($freshPayment->status)->toBe('refunded')
        ->and($freshPayment->amount_refunded)->toBe(200000);

    expect($userSub->fresh()->payment_status)->toBe('refunded');
});

test('authorized admin can issue partial refund for captured payment', function () {
    $payment = Payment::create([
        'user_id' => $this->student->id,
        'gateway_order_id' => 'order_rfnd_part_02',
        'gateway_payment_id' => 'pay_rfnd_part_02',
        'amount' => 200000, // ₹2000.00
        'currency' => 'INR',
        'status' => 'captured',
        'is_captured' => true,
    ]);

    $this->mockGateway->shouldReceive('createRefund')
        ->once()
        ->with('pay_rfnd_part_02', 50000, Mockery::any()) // ₹500.00
        ->andReturn([
            'id' => 'rfnd_part_gateway_02',
            'amount' => 50000,
            'status' => 'processed',
        ]);

    $response = $this->withHeaders($this->adminHeaders)
        ->postJson(route('admin.payments.refund', $payment->id), [
            'amount' => 500.00,
            'reason' => 'Partial adjustment',
        ]);

    $response->assertStatus(200);

    $freshPayment = $payment->fresh();
    expect($freshPayment->status)->toBe('partially_refunded')
        ->and($freshPayment->amount_refunded)->toBe(50000)
        ->and($freshPayment->refundable_balance)->toBe(150000);
});

test('refund amount exceeding refundable balance is blocked with 400', function () {
    $payment = Payment::create([
        'user_id' => $this->student->id,
        'gateway_order_id' => 'order_rfnd_excess_03',
        'gateway_payment_id' => 'pay_rfnd_excess_03',
        'amount' => 200000,
        'amount_refunded' => 150000, // already ₹1500 refunded, only ₹500 left
        'currency' => 'INR',
        'status' => 'partially_refunded',
        'is_captured' => true,
    ]);

    $response = $this->withHeaders($this->adminHeaders)
        ->postJson(route('admin.payments.refund', $payment->id), [
            'amount' => 600.00, // ₹600 exceeds ₹500 balance!
            'reason' => 'Over-refunding',
        ]);

    $response->assertStatus(400)
        ->assertJsonPath('success', false)
        ->assertJsonPath('code', 'REFUND_ERROR');
});

test('non-admin user is blocked from admin refund and dashboard routes', function () {
    $payment = Payment::create([
        'user_id' => $this->student->id,
        'gateway_order_id' => 'order_blocked_04',
        'amount' => 200000,
        'currency' => 'INR',
        'status' => 'captured',
    ]);

    // Student trying to access admin dashboard
    $res1 = $this->withHeaders($this->studentHeaders)->getJson(route('admin.payments.dashboard'));
    $res1->assertStatus(403);

    // Student trying to issue a refund
    $res2 = $this->withHeaders($this->studentHeaders)->postJson(route('admin.payments.refund', $payment->id), [
        'amount' => 100,
    ]);
    $res2->assertStatus(403);
});

test('admin payment dashboard calculates exact financial aggregates', function () {
    // Payment 1: Captured ₹1000
    Payment::create([
        'user_id' => $this->student->id,
        'purpose' => 'subscription_purchase',
        'amount' => 100000,
        'amount_refunded' => 20000, // ₹200 refunded
        'status' => 'partially_refunded',
        'is_captured' => true,
    ]);

    // Payment 2: Captured ₹2000
    Payment::create([
        'user_id' => $this->student->id,
        'purpose' => 'subscription_purchase',
        'amount' => 200000,
        'amount_refunded' => 0,
        'status' => 'captured',
        'is_captured' => true,
    ]);

    // Payment 3: Failed ₹500
    Payment::create([
        'user_id' => $this->student->id,
        'purpose' => 'one_time_purchase',
        'amount' => 50000,
        'status' => 'failed',
    ]);

    $response = $this->withHeaders($this->adminHeaders)->getJson(route('admin.payments.dashboard'));

    $response->assertStatus(200)
        ->assertJsonPath('data.counts.captured', 1)
        ->assertJsonPath('data.counts.failed', 1)
        ->assertJsonPath('data.counts.refunded', 1)
        ->assertJsonPath('data.revenue.captured_rupees', 3000)
        ->assertJsonPath('data.revenue.refunded_rupees', 200)
        ->assertJsonPath('data.revenue.net_rupees', 2800);
});

test('admin can reconcile a pending payment with Razorpay', function () {
    $orderId = 'order_reconcile_05';
    $paymentId = 'pay_reconcile_05';

    $payment = Payment::create([
        'user_id' => $this->student->id,
        'gateway_order_id' => $orderId,
        'amount' => 200000,
        'currency' => 'INR',
        'status' => 'pending',
    ]);

    $this->mockGateway->shouldReceive('fetchOrderPayments')
        ->with($orderId)
        ->andReturn([
            'items' => [
                [
                    'id' => $paymentId,
                    'status' => 'captured',
                    'method' => 'netbanking',
                    'amount' => 200000,
                ],
            ],
        ]);

    $response = $this->withHeaders($this->adminHeaders)
        ->postJson(route('admin.payments.reconcile', $payment->id));

    $response->assertStatus(200);

    $freshPayment = $payment->fresh();
    expect($freshPayment->status)->toBe('captured')
        ->and($freshPayment->is_captured)->toBeTrue()
        ->and($freshPayment->gateway_payment_id)->toBe($paymentId);
});

test('payment:reconcile artisan command recovers pending captured payments', function () {
    $orderId = 'order_artisan_rec_06';
    $paymentId = 'pay_artisan_rec_06';

    $payment = Payment::create([
        'user_id' => $this->student->id,
        'gateway_order_id' => $orderId,
        'amount' => 200000,
        'currency' => 'INR',
        'status' => 'pending',
        'created_at' => now()->subMinutes(30),
    ]);

    $this->mockGateway->shouldReceive('fetchOrderPayments')
        ->with($orderId)
        ->andReturn([
            'items' => [
                [
                    'id' => $paymentId,
                    'status' => 'captured',
                    'method' => 'card',
                ],
            ],
        ]);

    $exitCode = Artisan::call('payment:reconcile', [
        '--minutes' => 0,
    ]);

    expect($exitCode)->toBe(0);
    expect($payment->fresh()->status)->toBe('captured');
});
