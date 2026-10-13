<?php

use App\Jobs\SendSubscriptionConfirmation;
use App\Models\Payment;
use App\Models\Refund;
use App\Models\Subscription;
use App\Models\User;
use App\Models\UserSubscription;
use App\Models\WebhookEvent;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;

uses(RefreshDatabase::class);

beforeEach(function () {
    Queue::fake();

    $this->webhookSecret = config('vidyamission.razorpay.webhook_secret');

    // Create student
    $this->student = User::create([
        'name' => 'Rahul Verma',
        'email' => 'rahul@example.com',
        'mobile' => '9876543211',
        'role' => 'student',
        'password' => bcrypt('password123'),
        'is_active' => true,
        'is_verified' => true,
    ]);

    // Create subscription plan
    $this->plan = Subscription::create([
        'name' => 'Quarterly Plan',
        'price' => 1299.00,
        'duration_days' => 90,
        'is_active' => true,
    ]);
});

test('valid payment.captured webhook captures payment, activates subscription, and dispatches email', function () {
    $orderId = 'order_wh_captured_101';
    $paymentId = 'pay_wh_captured_101';

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
        'amount' => 129900,
        'currency' => 'INR',
        'status' => 'pending',
    ]);

    $payload = [
        'entity' => 'event',
        'account_id' => 'acc_test_123',
        'event' => 'payment.captured',
        'contains' => ['payment'],
        'payload' => [
            'payment' => [
                'entity' => [
                    'id' => $paymentId,
                    'order_id' => $orderId,
                    'amount' => 129900,
                    'currency' => 'INR',
                    'status' => 'captured',
                    'method' => 'card',
                ],
            ],
        ],
    ];

    $rawContent = json_encode($payload);
    $signature = hash_hmac('sha256', $rawContent, $this->webhookSecret);

    $response = $this->call(
        'POST',
        route('subscriptions.webhook'),
        [],
        [],
        [],
        [
            'HTTP_X-Razorpay-Signature' => $signature,
            'CONTENT_TYPE' => 'application/json',
        ],
        $rawContent
    );

    $response->assertStatus(200)
        ->assertJsonPath('success', true);

    // Verify payment updated
    $freshPayment = $payment->fresh();
    expect($freshPayment->status)->toBe('captured')
        ->and($freshPayment->is_captured)->toBeTrue()
        ->and($freshPayment->gateway_payment_id)->toBe($paymentId);

    // Verify subscription activated
    $freshSub = $userSub->fresh();
    expect($freshSub->payment_status)->toBe('completed')
        ->and($freshSub->payment_id)->toBe($paymentId)
        ->and($freshSub->isActive())->toBeTrue();

    // Verify webhook event record created
    $this->assertDatabaseHas('webhook_events', [
        'event_type' => 'payment.captured',
        'status' => 'processed',
    ]);

    Queue::assertPushed(SendSubscriptionConfirmation::class, 1);
});

test('webhook missing signature header is rejected with 400', function () {
    $rawContent = json_encode(['event' => 'payment.captured']);

    $response = $this->call(
        'POST',
        route('subscriptions.webhook'),
        [],
        [],
        [],
        ['CONTENT_TYPE' => 'application/json'],
        $rawContent
    );

    $response->assertStatus(400)
        ->assertJsonPath('code', 'INVALID_SIGNATURE');
});

test('webhook with invalid signature is rejected with 400 and not processed', function () {
    $rawContent = json_encode(['event' => 'payment.captured']);

    $response = $this->call(
        'POST',
        route('subscriptions.webhook'),
        [],
        [],
        [],
        [
            'HTTP_X-Razorpay-Signature' => 'invalid_tampered_signature',
            'CONTENT_TYPE' => 'application/json',
        ],
        $rawContent
    );

    $response->assertStatus(400)
        ->assertJsonPath('code', 'WEBHOOK_ERROR');
});

test('duplicate webhook deliveries are idempotent and do not duplicate fulfillment', function () {
    $orderId = 'order_wh_dup_202';
    $paymentId = 'pay_wh_dup_202';

    $userSub = UserSubscription::create([
        'user_id' => $this->student->id,
        'subscription_id' => $this->plan->id,
        'gateway_order_id' => $orderId,
        'payment_status' => 'pending',
    ]);

    $payment = Payment::create([
        'user_id' => $this->student->id,
        'payable_type' => UserSubscription::class,
        'payable_id' => $userSub->id,
        'gateway_order_id' => $orderId,
        'amount' => 129900,
        'currency' => 'INR',
        'status' => 'pending',
    ]);

    $payload = [
        'event_id' => 'evt_unique_123456789',
        'event' => 'payment.captured',
        'payload' => [
            'payment' => [
                'entity' => [
                    'id' => $paymentId,
                    'order_id' => $orderId,
                    'amount' => 129900,
                    'currency' => 'INR',
                    'status' => 'captured',
                ],
            ],
        ],
    ];

    $rawContent = json_encode($payload);
    $signature = hash_hmac('sha256', $rawContent, $this->webhookSecret);

    // First webhook call
    $res1 = $this->call('POST', route('subscriptions.webhook'), [], [], [], [
        'HTTP_X-Razorpay-Signature' => $signature,
        'CONTENT_TYPE' => 'application/json',
    ], $rawContent);
    $res1->assertStatus(200);

    $firstEndsAt = $userSub->fresh()->ends_at;

    // Second webhook call (Razorpay retry with same event)
    $res2 = $this->call('POST', route('subscriptions.webhook'), [], [], [], [
        'HTTP_X-Razorpay-Signature' => $signature,
        'CONTENT_TYPE' => 'application/json',
    ], $rawContent);
    $res2->assertStatus(200);

    // Ends at timestamp MUST NOT be recalculated or shifted!
    expect($userSub->fresh()->ends_at->toDateTimeString())->toBe($firstEndsAt->toDateTimeString());

    // Email job pushed exactly once
    Queue::assertPushed(SendSubscriptionConfirmation::class, 1);
});

test('out-of-order payment.failed webhook does not demote an already captured payment', function () {
    $orderId = 'order_wh_order_303';
    $paymentId = 'pay_wh_order_303';

    $payment = Payment::create([
        'user_id' => $this->student->id,
        'gateway_order_id' => $orderId,
        'gateway_payment_id' => $paymentId,
        'amount' => 129900,
        'currency' => 'INR',
        'status' => 'captured',
        'is_captured' => true,
    ]);

    $payload = [
        'event' => 'payment.failed',
        'payload' => [
            'payment' => [
                'entity' => [
                    'id' => $paymentId,
                    'order_id' => $orderId,
                    'error_code' => 'BAD_REQUEST_ERROR',
                    'error_description' => 'User aborted transaction',
                ],
            ],
        ],
    ];

    $rawContent = json_encode($payload);
    $signature = hash_hmac('sha256', $rawContent, $this->webhookSecret);

    $response = $this->call('POST', route('subscriptions.webhook'), [], [], [], [
        'HTTP_X-Razorpay-Signature' => $signature,
        'CONTENT_TYPE' => 'application/json',
    ], $rawContent);

    $response->assertStatus(200);

    // Payment MUST remain captured
    expect($payment->fresh()->status)->toBe('captured');
});

test('refund.processed webhook updates internal refund record', function () {
    $refundId = 'rfnd_mock_proc_404';

    $payment = Payment::create([
        'user_id' => $this->student->id,
        'gateway_order_id' => 'order_rfnd_test',
        'gateway_payment_id' => 'pay_rfnd_test',
        'amount' => 129900,
        'currency' => 'INR',
        'status' => 'captured',
        'is_captured' => true,
    ]);

    $refund = Refund::create([
        'payment_id' => $payment->id,
        'user_id' => $this->student->id,
        'gateway_refund_id' => $refundId,
        'amount' => 129900,
        'currency' => 'INR',
        'status' => 'pending',
    ]);

    $payload = [
        'event' => 'refund.processed',
        'payload' => [
            'refund' => [
                'entity' => [
                    'id' => $refundId,
                    'payment_id' => 'pay_rfnd_test',
                    'amount' => 129900,
                    'status' => 'processed',
                ],
            ],
        ],
    ];

    $rawContent = json_encode($payload);
    $signature = hash_hmac('sha256', $rawContent, $this->webhookSecret);

    $response = $this->call('POST', route('payments.webhook'), [], [], [], [
        'HTTP_X-Razorpay-Signature' => $signature,
        'CONTENT_TYPE' => 'application/json',
    ], $rawContent);

    $response->assertStatus(200);

    expect($refund->fresh()->status)->toBe('processed');
});
