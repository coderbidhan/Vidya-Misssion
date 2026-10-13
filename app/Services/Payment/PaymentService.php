<?php

namespace App\Services\Payment;

use App\Jobs\SendSubscriptionConfirmation;
use App\Models\Payment;
use App\Models\Refund;
use App\Models\Subscription;
use App\Models\User;
use App\Models\UserSubscription;
use App\Models\WebhookEvent;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class PaymentService
{
    public function __construct(
        protected RazorpayGateway $gateway
    ) {}

    public function getGateway(): RazorpayGateway
    {
        return $this->gateway;
    }

    /**
     * Initiate a subscription plan payment order.
     *
     * @return array Checkout data for the client
     *
     * @throws Exception
     */
    public function initiateSubscriptionPayment(User $user, Subscription $plan): array
    {
        if (! $plan->is_active) {
            throw new Exception('The selected subscription plan is inactive.');
        }

        $amountPaise = (int) round($plan->price * 100);
        $currency = config('vidyamission.razorpay.currency', 'INR');

        // Determine whether this is a new purchase or renewal
        $hasActiveSub = $user->userSubscriptions()
            ->where('payment_status', 'completed')
            ->where('ends_at', '>=', now())
            ->exists();

        $purpose = $hasActiveSub ? 'subscription_renewal' : 'subscription_purchase';

        return DB::transaction(function () use ($user, $plan, $amountPaise, $currency, $purpose) {
            // 1. Create a pending UserSubscription record
            $userSubscription = UserSubscription::create([
                'user_id' => $user->id,
                'subscription_id' => $plan->id,
                'payment_gateway' => 'razorpay',
                'payment_status' => 'pending',
            ]);

            $receipt = 'rcpt_sub_'.$userSubscription->id.'_'.time();

            // 2. Create the internal Payment transaction
            $payment = Payment::create([
                'uuid' => (string) Str::uuid(),
                'user_id' => $user->id,
                'payable_type' => UserSubscription::class,
                'payable_id' => $userSubscription->id,
                'purpose' => $purpose,
                'gateway' => 'razorpay',
                'amount' => $amountPaise,
                'currency' => $currency,
                'status' => 'pending',
                'receipt' => $receipt,
                'metadata' => [
                    'plan_id' => $plan->id,
                    'plan_name' => $plan->name,
                    'duration_days' => $plan->duration_days,
                ],
            ]);

            // 3. Create the Razorpay Order
            $notes = [
                'user_id' => (string) $user->id,
                'subscription_id' => (string) $plan->id,
                'user_subscription_id' => (string) $userSubscription->id,
                'payment_uuid' => $payment->uuid,
                'plan_name' => $plan->name,
            ];

            $order = $this->gateway->createOrder($amountPaise, $currency, $receipt, $notes);
            $orderId = $order['id'];

            // 4. Update internal records with gateway order ID
            $payment->update([
                'gateway_order_id' => $orderId,
                'gateway_status' => $order['status'] ?? 'created',
            ]);

            $userSubscription->update([
                'gateway_order_id' => $orderId,
                'payment_id' => $orderId, // Preserve backward compatibility
                'payment_record_id' => $payment->id,
            ]);

            return [
                'order_id' => $orderId,
                'user_subscription_id' => $userSubscription->id,
                'payment_uuid' => $payment->uuid,
                'amount' => (float) $plan->price,
                'amount_paise' => $amountPaise,
                'currency' => $currency,
                'key_id' => $this->gateway->getKeyId(),
                'plan_name' => $plan->name,
                'receipt' => $receipt,
                'customer' => [
                    'name' => $user->name,
                    'email' => $user->email,
                    'contact' => $user->mobile,
                ],
            ];
        });
    }

    /**
     * Initiate a generic one-time payment for any payable entity.
     *
     * @throws Exception
     */
    public function initiateOneTimePayment(
        User $user,
        ?Model $payable,
        int $amountPaise,
        string $purpose = 'one_time_purchase',
        string $description = 'One-time payment',
        array $notes = []
    ): array {
        if ($amountPaise <= 0) {
            throw new Exception('Payment amount must be greater than zero.');
        }

        $currency = config('vidyamission.razorpay.currency', 'INR');
        $receipt = 'rcpt_otp_'.($payable ? class_basename($payable).'_'.$payable->getKey().'_' : '').time();

        return DB::transaction(function () use ($user, $payable, $amountPaise, $currency, $purpose, $description, $receipt, $notes) {
            $payment = Payment::create([
                'uuid' => (string) Str::uuid(),
                'user_id' => $user->id,
                'payable_type' => $payable ? get_class($payable) : null,
                'payable_id' => $payable?->getKey(),
                'purpose' => $purpose,
                'gateway' => 'razorpay',
                'amount' => $amountPaise,
                'currency' => $currency,
                'status' => 'pending',
                'receipt' => $receipt,
                'metadata' => array_merge($notes, ['description' => $description]),
            ]);

            $orderNotes = array_merge($notes, [
                'user_id' => (string) $user->id,
                'payment_uuid' => $payment->uuid,
                'purpose' => $purpose,
            ]);

            $order = $this->gateway->createOrder($amountPaise, $currency, $receipt, $orderNotes);
            $orderId = $order['id'];

            $payment->update([
                'gateway_order_id' => $orderId,
                'gateway_status' => $order['status'] ?? 'created',
            ]);

            return [
                'order_id' => $orderId,
                'payment_uuid' => $payment->uuid,
                'amount' => round($amountPaise / 100, 2),
                'amount_paise' => $amountPaise,
                'currency' => $currency,
                'key_id' => $this->gateway->getKeyId(),
                'receipt' => $receipt,
                'customer' => [
                    'name' => $user->name,
                    'email' => $user->email,
                    'contact' => $user->mobile,
                ],
            ];
        });
    }

    /**
     * Verify payment signature and complete the transaction.
     *
     * @throws Exception
     */
    public function verifyAndCompletePayment(
        User $user,
        string $orderId,
        string $paymentId,
        string $signature
    ): Payment {
        // 1. Verify checkout cryptographic signature
        if (! $this->gateway->verifyPaymentSignature($orderId, $paymentId, $signature)) {
            throw new Exception('Invalid payment signature verification failed.');
        }

        return DB::transaction(function () use ($user, $orderId, $paymentId, $signature) {
            // 2. Fetch internal payment record with lock
            $payment = Payment::where('gateway_order_id', $orderId)
                ->where('user_id', $user->id)
                ->lockForUpdate()
                ->first();

            if (! $payment) {
                throw new Exception("No payment transaction found for order ID: {$orderId}");
            }

            // If already captured, return idempotently
            if ($payment->isCaptured()) {
                return $payment;
            }

            // 3. Confirm payment details with gateway
            $gatewayPayment = $this->gateway->fetchPayment($paymentId);

            // Verify order matches
            if (($gatewayPayment['order_id'] ?? null) !== $orderId) {
                throw new Exception('Payment does not belong to the specified order.');
            }

            // Verify amount & currency
            if ((int) ($gatewayPayment['amount'] ?? 0) !== (int) $payment->amount) {
                throw new Exception('Payment amount mismatch between gateway and order.');
            }

            if (strtoupper($gatewayPayment['currency'] ?? '') !== strtoupper($payment->currency)) {
                throw new Exception('Payment currency mismatch.');
            }

            // If payment is authorized but not yet captured, perform capture
            if (($gatewayPayment['status'] ?? '') === 'authorized' && config('vidyamission.razorpay.auto_capture', true)) {
                $gatewayPayment = $this->gateway->capturePayment($paymentId, $payment->amount, $payment->currency);
            }

            $gatewayStatus = $gatewayPayment['status'] ?? 'captured';
            if (! in_array($gatewayStatus, ['captured', 'authorized'])) {
                throw new Exception("Payment status is {$gatewayStatus}, cannot complete transaction.");
            }

            // 4. Mark internal payment as captured
            $payment->update([
                'gateway_payment_id' => $paymentId,
                'gateway_signature' => $signature,
                'gateway_status' => $gatewayStatus,
                'payment_method' => $gatewayPayment['method'] ?? null,
                'status' => 'captured',
                'is_captured' => true,
                'captured_at' => now(),
            ]);

            // 5. Complete and fulfill payable entity
            $this->fulfillPayable($payment, $paymentId);

            return $payment->fresh();
        });
    }

    /**
     * Handle payment failure callback from checkout.
     *
     * @throws Exception
     */
    public function handlePaymentFailure(
        User $user,
        string $orderId,
        ?string $failureCode = null,
        ?string $failureReason = null
    ): Payment {
        return DB::transaction(function () use ($user, $orderId, $failureCode, $failureReason) {
            $payment = Payment::where('gateway_order_id', $orderId)
                ->where('user_id', $user->id)
                ->lockForUpdate()
                ->first();

            if (! $payment) {
                throw new Exception("Payment order {$orderId} not found.");
            }

            // NEVER overwrite a captured payment with failed status
            if ($payment->isCaptured()) {
                Log::warning('Ignored failure report for already captured payment', [
                    'order_id' => $orderId,
                    'payment_id' => $payment->gateway_payment_id,
                ]);

                return $payment;
            }

            $payment->update([
                'status' => 'failed',
                'failed_at' => now(),
                'failure_code' => $failureCode,
                'failure_reason' => $failureReason,
            ]);

            // If linked to UserSubscription, update subscription status
            if ($payment->payable_type === UserSubscription::class && $payment->payable_id) {
                UserSubscription::where('id', $payment->payable_id)
                    ->where('payment_status', 'pending')
                    ->update(['payment_status' => 'failed']);
            } elseif ($orderId) {
                UserSubscription::where('gateway_order_id', $orderId)
                    ->where('payment_status', 'pending')
                    ->update(['payment_status' => 'failed']);
            }

            return $payment->fresh();
        });
    }

    /**
     * Process an incoming Razorpay webhook payload with strict signature verification and idempotency.
     *
     * @throws Exception on signature verification or processing failure
     */
    public function processWebhook(string $rawPayload, string $signature): array
    {
        // 1. Verify webhook signature
        if (! $this->gateway->verifyWebhookSignature($rawPayload, $signature)) {
            Log::warning('Razorpay webhook signature verification failed.');
            throw new Exception('Invalid Razorpay webhook signature.');
        }

        $payload = json_decode($rawPayload, true);
        if (! is_array($payload)) {
            throw new Exception('Malformed webhook JSON payload.');
        }

        $event = $payload['event'] ?? null;
        if (! $event) {
            throw new Exception('Missing event type in webhook payload.');
        }

        // 2. Extract durable event identifier for idempotency
        $eventId = $payload['event_id'] ?? $payload['id'] ?? md5($rawPayload);

        return DB::transaction(function () use ($eventId, $event, $payload) {
            // Find or create webhook event record with lock
            $eventRecord = WebhookEvent::where('gateway', 'razorpay')
                ->where('event_id', $eventId)
                ->lockForUpdate()
                ->first();

            if ($eventRecord && $eventRecord->isProcessed()) {
                return [
                    'status' => 'ignored',
                    'message' => 'Duplicate webhook event already processed.',
                ];
            }

            if (! $eventRecord) {
                $eventRecord = WebhookEvent::create([
                    'gateway' => 'razorpay',
                    'event_id' => $eventId,
                    'event_type' => $event,
                    'payload' => $payload,
                    'status' => 'received',
                ]);
            }

            try {
                $this->handleWebhookEvent($event, $payload);

                $eventRecord->update([
                    'status' => 'processed',
                    'processed_at' => now(),
                ]);

                return [
                    'status' => 'processed',
                    'event' => $event,
                ];
            } catch (Exception $e) {
                $eventRecord->update([
                    'status' => 'failed',
                    'error_message' => $e->getMessage(),
                ]);

                throw $e;
            }
        });
    }

    /**
     * Dispatch specific webhook event handler.
     */
    protected function handleWebhookEvent(string $event, array $payload): void
    {
        switch ($event) {
            case 'payment.captured':
            case 'payment.authorized':
                $this->handlePaymentCapturedWebhook($payload);
                break;

            case 'payment.failed':
                $this->handlePaymentFailedWebhook($payload);
                break;

            case 'order.paid':
                $this->handleOrderPaidWebhook($payload);
                break;

            case 'refund.processed':
                $this->handleRefundProcessedWebhook($payload);
                break;

            case 'refund.failed':
                $this->handleRefundFailedWebhook($payload);
                break;

            default:
                Log::info("Unhandled Razorpay webhook event: {$event}");
                break;
        }
    }

    /**
     * Handle payment.captured / payment.authorized webhook.
     */
    protected function handlePaymentCapturedWebhook(array $payload): void
    {
        $paymentEntity = $payload['payload']['payment']['entity'] ?? [];
        $paymentId = $paymentEntity['id'] ?? null;
        $orderId = $paymentEntity['order_id'] ?? null;

        if (! $paymentId && ! $orderId) {
            return;
        }

        // Locate internal payment record
        $query = Payment::query()->lockForUpdate();
        if ($orderId) {
            $query->where('gateway_order_id', $orderId);
        } else {
            $query->where('gateway_payment_id', $paymentId);
        }
        $payment = $query->first();

        // Fallback for legacy user_subscriptions records
        if (! $payment && $orderId) {
            $userSub = UserSubscription::where('gateway_order_id', $orderId)
                ->orWhere('payment_id', $orderId)
                ->first();

            if ($userSub) {
                $this->fulfillUserSubscriptionDirectly($userSub, $paymentId);
            }

            return;
        }

        if (! $payment) {
            Log::warning("Razorpay webhook: no payment found for order: {$orderId}, payment: {$paymentId}");

            return;
        }

        // If already captured, do nothing (idempotent)
        if ($payment->isCaptured()) {
            return;
        }

        // Auto-capture if authorized
        if (($paymentEntity['status'] ?? '') === 'authorized' && config('vidyamission.razorpay.auto_capture', true)) {
            try {
                $paymentEntity = $this->gateway->capturePayment($paymentId, $payment->amount, $payment->currency);
            } catch (Exception $e) {
                Log::error('Auto-capture failed in webhook', ['error' => $e->getMessage()]);
            }
        }

        $payment->update([
            'gateway_payment_id' => $paymentId,
            'gateway_status' => $paymentEntity['status'] ?? 'captured',
            'payment_method' => $paymentEntity['method'] ?? $payment->payment_method,
            'status' => 'captured',
            'is_captured' => true,
            'captured_at' => now(),
        ]);

        $this->fulfillPayable($payment, $paymentId);
    }

    /**
     * Handle payment.failed webhook.
     */
    protected function handlePaymentFailedWebhook(array $payload): void
    {
        $paymentEntity = $payload['payload']['payment']['entity'] ?? [];
        $paymentId = $paymentEntity['id'] ?? null;
        $orderId = $paymentEntity['order_id'] ?? null;
        $errorCode = $paymentEntity['error_code'] ?? null;
        $errorDesc = $paymentEntity['error_description'] ?? null;

        if (! $orderId && ! $paymentId) {
            return;
        }

        $payment = Payment::where(function ($q) use ($orderId, $paymentId) {
            if ($orderId) {
                $q->where('gateway_order_id', $orderId);
            }
            if ($paymentId) {
                $q->orWhere('gateway_payment_id', $paymentId);
            }
        })->lockForUpdate()->first();

        if ($payment) {
            // NEVER overwrite a captured payment with failed status
            if ($payment->isCaptured()) {
                Log::info("Ignoring payment.failed webhook for captured payment {$payment->id}");

                return;
            }

            $payment->update([
                'status' => 'failed',
                'failed_at' => now(),
                'failure_code' => $errorCode,
                'failure_reason' => $errorDesc,
            ]);

            if ($payment->payable_type === UserSubscription::class && $payment->payable_id) {
                UserSubscription::where('id', $payment->payable_id)
                    ->where('payment_status', 'pending')
                    ->update(['payment_status' => 'failed']);
            }
        }
    }

    /**
     * Handle order.paid webhook.
     */
    protected function handleOrderPaidWebhook(array $payload): void
    {
        $orderEntity = $payload['payload']['order']['entity'] ?? [];
        $orderId = $orderEntity['id'] ?? null;

        if (! $orderId) {
            return;
        }

        $payment = Payment::where('gateway_order_id', $orderId)->lockForUpdate()->first();
        if ($payment && ! $payment->isCaptured()) {
            // Fetch payments attempted for this order
            try {
                $orderPayments = $this->gateway->fetchOrderPayments($orderId);
                $items = $orderPayments['items'] ?? [];
                foreach ($items as $item) {
                    if (($item['status'] ?? '') === 'captured') {
                        $payment->update([
                            'gateway_payment_id' => $item['id'],
                            'gateway_status' => 'captured',
                            'status' => 'captured',
                            'is_captured' => true,
                            'captured_at' => now(),
                        ]);
                        $this->fulfillPayable($payment, $item['id']);
                        break;
                    }
                }
            } catch (Exception $e) {
                Log::error('Order.paid reconciliation failed', ['order_id' => $orderId, 'error' => $e->getMessage()]);
            }
        }
    }

    /**
     * Handle refund.processed webhook.
     */
    protected function handleRefundProcessedWebhook(array $payload): void
    {
        $refundEntity = $payload['payload']['refund']['entity'] ?? [];
        $refundId = $refundEntity['id'] ?? null;
        $paymentId = $refundEntity['payment_id'] ?? null;
        $amountPaise = (int) ($refundEntity['amount'] ?? 0);

        if (! $refundId) {
            return;
        }

        $refund = Refund::where('gateway_refund_id', $refundId)->first();
        if ($refund) {
            $refund->update([
                'status' => 'processed',
                'processed_at' => now(),
            ]);
        }

        if ($paymentId) {
            $payment = Payment::where('gateway_payment_id', $paymentId)->lockForUpdate()->first();
            if ($payment) {
                // If refund was not tracked internally yet, update amount_refunded
                if (! $refund) {
                    $newRefunded = $payment->amount_refunded + $amountPaise;
                    $status = $newRefunded >= $payment->amount ? 'refunded' : 'partially_refunded';
                    $payment->update([
                        'amount_refunded' => $newRefunded,
                        'status' => $status,
                    ]);
                }
            }
        }
    }

    /**
     * Handle refund.failed webhook.
     */
    protected function handleRefundFailedWebhook(array $payload): void
    {
        $refundEntity = $payload['payload']['refund']['entity'] ?? [];
        $refundId = $refundEntity['id'] ?? null;

        if (! $refundId) {
            return;
        }

        $refund = Refund::where('gateway_refund_id', $refundId)->first();
        if ($refund) {
            $refund->update([
                'status' => 'failed',
                'failed_at' => now(),
                'failure_reason' => $refundEntity['error_description'] ?? 'Refund failed at gateway.',
            ]);
        }
    }

    /**
     * Fulfill the underlying payable entity (e.g. activate/renew UserSubscription).
     */
    protected function fulfillPayable(Payment $payment, string $paymentId): void
    {
        if ($payment->payable_type === UserSubscription::class && $payment->payable_id) {
            $userSub = UserSubscription::find($payment->payable_id);
            if ($userSub) {
                $this->fulfillUserSubscriptionDirectly($userSub, $paymentId, $payment);
            }
        }
    }

    /**
     * Activate or renew a UserSubscription with safe duration math and single confirmation dispatch.
     */
    public function fulfillUserSubscriptionDirectly(
        UserSubscription $userSub,
        string $paymentId,
        ?Payment $payment = null
    ): void {
        // If already completed, don't duplicate activation or email
        if ($userSub->payment_status === 'completed' && $userSub->ends_at !== null) {
            return;
        }

        $plan = $userSub->subscription;
        if (! $plan) {
            return;
        }

        $user = $userSub->user;

        // Check if user has an existing active completed subscription (to extend validity)
        $currentActiveSub = $user->userSubscriptions()
            ->where('id', '!=', $userSub->id)
            ->where('payment_status', 'completed')
            ->where('ends_at', '>', now())
            ->orderByDesc('ends_at')
            ->first();

        if ($currentActiveSub && $currentActiveSub->ends_at) {
            // Renewal: starts from current subscription's end, extending it
            $startsAt = $userSub->starts_at ?? now();
            $endsAt = $currentActiveSub->ends_at->copy()->addDays($plan->duration_days);
        } else {
            // New purchase: starts now
            $startsAt = now();
            $endsAt = now()->addDays($plan->duration_days);
        }

        $userSub->update([
            'payment_id' => $paymentId,
            'gateway_order_id' => $payment?->gateway_order_id ?? $userSub->gateway_order_id,
            'payment_status' => 'completed',
            'starts_at' => $startsAt,
            'ends_at' => $endsAt,
        ]);

        // Dispatch confirmation email
        SendSubscriptionConfirmation::dispatch($user, $userSub);
    }

    /**
     * Reconcile an individual payment with live Razorpay status.
     *
     * @throws Exception
     */
    public function reconcilePayment(Payment $payment): Payment
    {
        if (! $payment->gateway_order_id) {
            return $payment;
        }

        try {
            $orderPayments = $this->gateway->fetchOrderPayments($payment->gateway_order_id);
            $items = $orderPayments['items'] ?? [];

            foreach ($items as $item) {
                if (($item['status'] ?? '') === 'captured') {
                    if (! $payment->isCaptured()) {
                        DB::transaction(function () use ($payment, $item) {
                            $payment->update([
                                'gateway_payment_id' => $item['id'],
                                'gateway_status' => 'captured',
                                'payment_method' => $item['method'] ?? $payment->payment_method,
                                'status' => 'captured',
                                'is_captured' => true,
                                'captured_at' => now(),
                            ]);

                            $this->fulfillPayable($payment, $item['id']);
                        });
                    }

                    return $payment->fresh();
                }
            }

            // If order has no captured payments and is older than 60 mins, mark failed if pending
            if ($payment->isPending() && $payment->created_at->lt(now()->subMinutes(60))) {
                $payment->update([
                    'status' => 'failed',
                    'failed_at' => now(),
                    'failure_reason' => 'Payment abandoned or timed out.',
                ]);

                if ($payment->payable_type === UserSubscription::class && $payment->payable_id) {
                    UserSubscription::where('id', $payment->payable_id)
                        ->where('payment_status', 'pending')
                        ->update(['payment_status' => 'failed']);
                }
            }

            return $payment->fresh();
        } catch (Exception $e) {
            Log::error('Payment reconciliation error', [
                'payment_id' => $payment->id,
                'order_id' => $payment->gateway_order_id,
                'error' => $e->getMessage(),
            ]);

            throw $e;
        }
    }
}
