<?php

namespace App\Services;

use App\Models\Payment;
use App\Models\Subscription;
use App\Models\User;
use App\Services\Payment\PaymentService;
use Exception;

class SubscriptionService
{
    public function __construct(
        private readonly PaymentService $paymentService
    ) {}

    /**
     * Initiate a payment order with the configured gateway.
     *
     * @return array Gateway-specific order data to return to the client.
     *
     * @throws Exception
     */
    public function initiatePayment(User $user, Subscription $plan): array
    {
        $gateway = config('vidyamission.payment_gateway', 'razorpay');

        if ($gateway === 'razorpay') {
            $data = $this->paymentService->initiateSubscriptionPayment($user, $plan);

            return [
                'order_id' => $data['order_id'],
                'user_subscription_id' => $data['user_subscription_id'],
                'amount' => $plan->price,
                'currency' => 'INR',
                'key_id' => $data['key_id'],
                'plan_name' => $plan->name,
                'amount_paise' => $data['amount_paise'],
                'payment_uuid' => $data['payment_uuid'],
            ];
        }

        throw new Exception("Unsupported payment gateway: {$gateway}");
    }

    /**
     * Verify payment signature from client checkout and activate subscription.
     *
     * @throws Exception
     */
    public function verifyPayment(User $user, string $orderId, string $paymentId, string $signature): Payment
    {
        return $this->paymentService->verifyAndCompletePayment($user, $orderId, $paymentId, $signature);
    }

    /**
     * Record a payment failure reported by client checkout.
     *
     * @throws Exception
     */
    public function handleFailure(User $user, string $orderId, ?string $code = null, ?string $reason = null): Payment
    {
        return $this->paymentService->handlePaymentFailure($user, $orderId, $code, $reason);
    }

    /**
     * Handle payment gateway webhook payload.
     *
     * @param  string|array  $payload  Raw payload string or array
     *
     * @throws Exception on signature verification failure
     */
    public function handleWebhook(string|array $payload, string $signature, string $gateway): void
    {
        if ($gateway === 'razorpay') {
            $rawPayload = is_string($payload) ? $payload : (string) json_encode($payload);
            $this->paymentService->processWebhook($rawPayload, $signature);

            return;
        }

        throw new Exception("Unsupported gateway webhook: {$gateway}");
    }

    /**
     * Check if a user has an active (paid) subscription.
     */
    public function isActive(User $user): bool
    {
        return $user->userSubscriptions()
            ->where('payment_status', 'completed')
            ->where('starts_at', '<=', now())
            ->where('ends_at', '>=', now())
            ->exists();
    }
}
