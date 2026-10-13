<?php

use App\Jobs\SendSubscriptionConfirmation;
use App\Models\Payment;
use App\Models\Subscription;
use App\Models\User;
use App\Models\UserSubscription;
use App\Services\Payment\RazorpayGateway;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;

uses(RefreshDatabase::class);

beforeEach(function () {
    Queue::fake();

    $this->keySecret = config('vidyamission.razorpay.key_secret');

    // Create a student user
    $this->student = User::create([
        'name' => 'Aditi Sharma',
        'email' => 'aditi@example.com',
        'mobile' => '9876543210',
        'role' => 'student',
        'password' => bcrypt('password123'),
        'is_active' => true,
        'is_verified' => true,
    ]);

    $this->token = $this->student->createToken('test-token')->plainTextToken;
    $this->headers = ['Authorization' => "Bearer {$this->token}"];

    // Create a subscription plan (₹499.00, 30 days)
    $this->plan = Subscription::create([
        'name' => 'Standard Monthly Plan',
        'price' => 499.00,
        'duration_days' => 30,
        'description' => 'Full access for 30 days',
        'is_active' => true,
    ]);

    // Mock RazorpayGateway order creation
    $this->mockGateway = Mockery::mock(RazorpayGateway::class, [
        config('vidyamission.razorpay.key_id'),
        config('vidyamission.razorpay.key_secret'),
        config('vidyamission.razorpay.webhook_secret'),
    ])->makePartial();

    $this->app->instance(RazorpayGateway::class, $this->mockGateway);
});

test('authenticated student can initiate subscription payment with authoritative server price', function () {
    $this->mockGateway->shouldReceive('createOrder')
        ->once()
        ->withArgs(function ($amountPaise, $currency, $receipt, $notes) {
            return $amountPaise === 49900 && $currency === 'INR';
        })
        ->andReturn([
            'id' => 'order_mock_test_123',
            'amount' => 49900,
            'currency' => 'INR',
            'status' => 'created',
            'receipt' => 'rcpt_sub_test',
        ]);

    $response = $this->withHeaders($this->headers)
        ->postJson(route('subscriptions.initiate', $this->plan->id));

    $response->assertStatus(200)
        ->assertJsonPath('success', true)
        ->assertJsonPath('data.order_id', 'order_mock_test_123')
        ->assertJsonPath('data.amount_paise', 49900)
        ->assertJsonPath('data.amount', '499.00')
        ->assertJsonPath('data.currency', 'INR');

    // Verify pending database records
    $this->assertDatabaseHas('payments', [
        'user_id' => $this->student->id,
        'gateway_order_id' => 'order_mock_test_123',
        'amount' => 49900,
        'status' => 'pending',
        'purpose' => 'subscription_purchase',
    ]);

    $this->assertDatabaseHas('user_subscriptions', [
        'user_id' => $this->student->id,
        'subscription_id' => $this->plan->id,
        'gateway_order_id' => 'order_mock_test_123',
        'payment_status' => 'pending',
    ]);
});

test('unauthenticated request to initiate subscription payment is blocked with 401', function () {
    $response = $this->postJson(route('subscriptions.initiate', $this->plan->id));
    $response->assertStatus(401);
});

test('initiating payment for inactive subscription plan is rejected', function () {
    $inactivePlan = Subscription::create([
        'name' => 'Deprecated Plan',
        'price' => 199.00,
        'duration_days' => 15,
        'is_active' => false,
    ]);

    $response = $this->withHeaders($this->headers)
        ->postJson(route('subscriptions.initiate', $inactivePlan->id));

    $response->assertStatus(404);
});

test('valid payment signature verification activates subscription and captures payment', function () {
    $orderId = 'order_valid_verify_001';
    $paymentId = 'pay_valid_verify_001';
    $signature = hash_hmac('sha256', $orderId.'|'.$paymentId, $this->keySecret);

    // Create pending internal records
    $userSub = UserSubscription::create([
        'user_id' => $this->student->id,
        'subscription_id' => $this->plan->id,
        'gateway_order_id' => $orderId,
        'payment_id' => $orderId,
        'payment_status' => 'pending',
    ]);

    $payment = Payment::create([
        'user_id' => $this->student->id,
        'payable_type' => UserSubscription::class,
        'payable_id' => $userSub->id,
        'purpose' => 'subscription_purchase',
        'gateway' => 'razorpay',
        'gateway_order_id' => $orderId,
        'amount' => 49900,
        'currency' => 'INR',
        'status' => 'pending',
        'receipt' => 'rcpt_test_001',
    ]);

    $userSub->update(['payment_record_id' => $payment->id]);

    $this->mockGateway->shouldReceive('fetchPayment')
        ->with($paymentId)
        ->andReturn([
            'id' => $paymentId,
            'order_id' => $orderId,
            'amount' => 49900,
            'currency' => 'INR',
            'status' => 'captured',
            'method' => 'upi',
        ]);

    $response = $this->withHeaders($this->headers)->postJson(route('subscriptions.verify'), [
        'razorpay_order_id' => $orderId,
        'razorpay_payment_id' => $paymentId,
        'razorpay_signature' => $signature,
    ]);

    $response->assertStatus(200)
        ->assertJsonPath('success', true)
        ->assertJsonPath('data.payment.status', 'captured')
        ->assertJsonPath('data.subscription.payment_status', 'completed');

    // Assert database updated
    $this->assertDatabaseHas('payments', [
        'id' => $payment->id,
        'gateway_payment_id' => $paymentId,
        'status' => 'captured',
        'is_captured' => true,
    ]);

    $freshSub = $userSub->fresh();
    expect($freshSub->payment_status)->toBe('completed')
        ->and($freshSub->payment_id)->toBe($paymentId)
        ->and($freshSub->isActive())->toBeTrue();

    // Confirmation email dispatched once
    Queue::assertPushed(SendSubscriptionConfirmation::class, 1);
});

test('invalid payment signature is rejected with 400 and does not activate subscription', function () {
    $orderId = 'order_bad_verify_002';
    $paymentId = 'pay_bad_verify_002';
    $invalidSignature = 'forged_or_invalid_signature_hex';

    UserSubscription::create([
        'user_id' => $this->student->id,
        'subscription_id' => $this->plan->id,
        'gateway_order_id' => $orderId,
        'payment_status' => 'pending',
    ]);

    Payment::create([
        'user_id' => $this->student->id,
        'gateway_order_id' => $orderId,
        'amount' => 49900,
        'currency' => 'INR',
        'status' => 'pending',
    ]);

    $response = $this->withHeaders($this->headers)->postJson(route('subscriptions.verify'), [
        'razorpay_order_id' => $orderId,
        'razorpay_payment_id' => $paymentId,
        'razorpay_signature' => $invalidSignature,
    ]);

    $response->assertStatus(400)
        ->assertJsonPath('success', false)
        ->assertJsonPath('code', 'VERIFICATION_FAILED');

    $this->assertDatabaseHas('payments', [
        'gateway_order_id' => $orderId,
        'status' => 'pending',
    ]);

    Queue::assertNotPushed(SendSubscriptionConfirmation::class);
});

test('subscription renewal extends validity from existing active expiration date', function () {
    // 1. Existing active subscription expiring in 15 days
    $existingSub = UserSubscription::create([
        'user_id' => $this->student->id,
        'subscription_id' => $this->plan->id,
        'payment_id' => 'pay_previous_active',
        'gateway_order_id' => 'order_previous_active',
        'payment_status' => 'completed',
        'starts_at' => now()->subDays(15),
        'ends_at' => now()->addDays(15),
    ]);

    $renewalOrderId = 'order_renewal_003';
    $renewalPaymentId = 'pay_renewal_003';
    $signature = hash_hmac('sha256', $renewalOrderId.'|'.$renewalPaymentId, $this->keySecret);

    $renewalSub = UserSubscription::create([
        'user_id' => $this->student->id,
        'subscription_id' => $this->plan->id,
        'gateway_order_id' => $renewalOrderId,
        'payment_status' => 'pending',
    ]);

    Payment::create([
        'user_id' => $this->student->id,
        'payable_type' => UserSubscription::class,
        'payable_id' => $renewalSub->id,
        'purpose' => 'subscription_renewal',
        'gateway_order_id' => $renewalOrderId,
        'amount' => 49900,
        'currency' => 'INR',
        'status' => 'pending',
    ]);

    $this->mockGateway->shouldReceive('fetchPayment')
        ->with($renewalPaymentId)
        ->andReturn([
            'id' => $renewalPaymentId,
            'order_id' => $renewalOrderId,
            'amount' => 49900,
            'currency' => 'INR',
            'status' => 'captured',
            'method' => 'card',
        ]);

    $response = $this->withHeaders($this->headers)->postJson(route('subscriptions.verify'), [
        'razorpay_order_id' => $renewalOrderId,
        'razorpay_payment_id' => $renewalPaymentId,
        'razorpay_signature' => $signature,
    ]);

    $response->assertStatus(200);

    $refreshedRenewal = $renewalSub->fresh();
    expect($refreshedRenewal->payment_status)->toBe('completed');

    // Renewal validity MUST extend from existing ends_at (+ 30 days = ~45 days from now)
    $expectedEndsAt = $existingSub->ends_at->copy()->addDays(30);
    expect($refreshedRenewal->ends_at->diffInDays($expectedEndsAt))->toBeLessThanOrEqual(1);
});

test('reporting payment checkout failure updates payment status to failed without deleting records', function () {
    $orderId = 'order_failed_report_004';

    UserSubscription::create([
        'user_id' => $this->student->id,
        'subscription_id' => $this->plan->id,
        'gateway_order_id' => $orderId,
        'payment_status' => 'pending',
    ]);

    $payment = Payment::create([
        'user_id' => $this->student->id,
        'gateway_order_id' => $orderId,
        'amount' => 49900,
        'currency' => 'INR',
        'status' => 'pending',
    ]);

    $response = $this->withHeaders($this->headers)->postJson(route('subscriptions.failed'), [
        'razorpay_order_id' => $orderId,
        'error_code' => 'BAD_REQUEST_ERROR',
        'error_description' => 'Payment was cancelled by the user.',
    ]);

    $response->assertStatus(200);

    $this->assertDatabaseHas('payments', [
        'id' => $payment->id,
        'status' => 'failed',
        'failure_code' => 'BAD_REQUEST_ERROR',
    ]);

    $this->assertDatabaseHas('user_subscriptions', [
        'gateway_order_id' => $orderId,
        'payment_status' => 'failed',
    ]);
});

test('failure report cannot overwrite an already captured payment', function () {
    $orderId = 'order_already_captured_005';
    $paymentId = 'pay_already_captured_005';

    $payment = Payment::create([
        'user_id' => $this->student->id,
        'gateway_order_id' => $orderId,
        'gateway_payment_id' => $paymentId,
        'amount' => 49900,
        'currency' => 'INR',
        'status' => 'captured',
        'is_captured' => true,
    ]);

    $response = $this->withHeaders($this->headers)->postJson(route('subscriptions.failed'), [
        'razorpay_order_id' => $orderId,
        'error_code' => 'LATE_FAILURE_EVENT',
        'error_description' => 'User aborted afterwards.',
    ]);

    $response->assertStatus(200);

    // Status MUST remain captured!
    expect($payment->fresh()->status)->toBe('captured');
});

test('one-time purchase payment initiation and verification flow', function () {
    $this->mockGateway->shouldReceive('createOrder')
        ->once()
        ->andReturn([
            'id' => 'order_otp_mock_006',
            'amount' => 15000,
            'currency' => 'INR',
            'status' => 'created',
            'receipt' => 'rcpt_otp_test',
        ]);

    // 1. Initiate one-time payment
    $initResponse = $this->withHeaders($this->headers)->postJson(route('payments.one-time'), [
        'amount' => 150.00,
        'purpose' => 'one_time_purchase',
        'description' => 'Single Mock Test Pass',
    ]);

    $initResponse->assertStatus(200)
        ->assertJsonPath('data.order_id', 'order_otp_mock_006')
        ->assertJsonPath('data.amount', 150)
        ->assertJsonPath('data.amount_paise', 15000);

    // 2. Verify payment
    $orderId = 'order_otp_mock_006';
    $paymentId = 'pay_otp_mock_006';
    $signature = hash_hmac('sha256', $orderId.'|'.$paymentId, $this->keySecret);

    $this->mockGateway->shouldReceive('fetchPayment')
        ->with($paymentId)
        ->andReturn([
            'id' => $paymentId,
            'order_id' => $orderId,
            'amount' => 15000,
            'currency' => 'INR',
            'status' => 'captured',
            'method' => 'upi',
        ]);

    $verifyResponse = $this->withHeaders($this->headers)->postJson(route('payments.verify'), [
        'razorpay_order_id' => $orderId,
        'razorpay_payment_id' => $paymentId,
        'razorpay_signature' => $signature,
    ]);

    $verifyResponse->assertStatus(200)
        ->assertJsonPath('data.status', 'captured')
        ->assertJsonPath('data.amount', 15000);
});
