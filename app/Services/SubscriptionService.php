<?php

namespace App\Services;

use App\Jobs\SendSubscriptionConfirmation;
use App\Models\Subscription;
use App\Models\User;
use App\Models\UserSubscription;
use Illuminate\Support\Facades\Log;
use Razorpay\Api\Api as RazorpayApi;

class SubscriptionService
{
    /**
     * Initiate a payment order with the configured gateway.
     *
     * @return array Gateway-specific order data to return to the client.
     *
     * @throws \Exception
     */
    public function initiatePayment(User $user, Subscription $plan): array
    {
        $gateway = config('vidyamission.payment_gateway', 'razorpay');

        // Create a pending subscription record
        $userSubscription = UserSubscription::create([
            'user_id' => $user->id,
            'subscription_id' => $plan->id,
            'payment_gateway' => $gateway,
            'payment_status' => 'pending',
        ]);

        if ($gateway === 'razorpay') {
            return $this->createRazorpayOrder($plan, $userSubscription);
        }

        throw new \Exception("Unsupported payment gateway: {$gateway}");
    }

    /**
     * Create a Razorpay order and return the order data.
     */
    private function createRazorpayOrder(Subscription $plan, UserSubscription $userSubscription): array
    {
        $api = new RazorpayApi(
            config('vidyamission.razorpay.key_id'),
            config('vidyamission.razorpay.key_secret')
        );

        $orderData = [
            'amount' => (int) ($plan->price * 100), // Razorpay uses paise
            'currency' => 'INR',
            'receipt' => 'sub_'.$userSubscription->id,
            'notes' => [
                'subscription_id' => $plan->id,
                'user_subscription_id' => $userSubscription->id,
                'plan_name' => $plan->name,
            ],
        ];

        $order = $api->order->create($orderData);

        $userSubscription->update(['payment_id' => $order->id]);

        return [
            'order_id' => $order->id,
            'user_subscription_id' => $userSubscription->id,
            'amount' => $plan->price,
            'currency' => 'INR',
            'key_id' => config('vidyamission.razorpay.key_id'),
            'plan_name' => $plan->name,
        ];
    }

    /**
     * Handle an incoming payment gateway webhook payload.
     *
     * Verifies the HMAC signature and updates the subscription status.
     *
     * @throws \Exception on signature verification failure
     */
    public function handleWebhook(array $payload, string $signature, string $gateway, ?string $rawBody = null): void
    {
        if ($gateway === 'razorpay') {
            $this->handleRazorpayWebhook($payload, $signature, $rawBody);

            return;
        }

        throw new \Exception("Unsupported gateway webhook: {$gateway}");
    }

    /**
     * Handle Razorpay-specific webhook verification and processing.
     */
    private function handleRazorpayWebhook(
        array $payload,
        string $signature,
        ?string $rawBody = null
    ): void {

        if (empty($rawBody)) {
            throw new \Exception('Webhook request body is empty.');
        }

        $webhookSecret = config('vidyamission.razorpay.webhook_secret') ?: config('services.razorpay.webhook_secret');

        // Verify HMAC signature
        if (app()->environment('local', 'testing') && (empty($signature) || empty($webhookSecret))) {
            Log::info('Razorpay webhook signature verification bypassed in local/testing environment.');
        } else {
            if (empty($webhookSecret)) {
                throw new \Exception('Razorpay webhook secret is not configured.');
            }

            /*
            |--------------------------------------------------------------------------
            | Verify Razorpay Webhook Signature
            |--------------------------------------------------------------------------
            */

            try {

                // Option 1 (Recommended): Razorpay SDK
                if (class_exists(RazorpayApi::class)) {

                    $api = new RazorpayApi(
                        config('vidyamission.razorpay.key_id') ?: config('services.razorpay.key'),
                        config('vidyamission.razorpay.key_secret') ?: config('services.razorpay.secret')
                    );

                    $api->utility->verifyWebhookSignature(
                        $rawBody,
                        $signature,
                        $webhookSecret
                    );

                } else {

                    // Option 2: Manual verification
                    $expectedSignature = hash_hmac(
                        'sha256',
                        $rawBody,
                        $webhookSecret
                    );

                    if (! hash_equals($expectedSignature, $signature)) {
                        throw new \Exception('Invalid Razorpay webhook signature.');
                    }
                }

            } catch (\Throwable $e) {

                $expectedSignature = isset($expectedSignature) ? $expectedSignature : hash_hmac('sha256', $rawBody, $webhookSecret);

                Log::warning('Razorpay webhook signature verification failed.', [
                    'received_signature' => $signature,
                    'webhook_secret_configured' => ! empty($webhookSecret),
                    'webhook_secret_preview' => $webhookSecret ? (substr($webhookSecret, 0, 3).'...') : 'empty',
                    'expected_signature' => $expectedSignature,
                    'raw_body_preview' => substr($rawBody, 0, 300),
                    'event' => $payload['event'] ?? null,
                    'message' => $e->getMessage(),
                ]);

                throw new \Exception('Invalid Razorpay webhook signature.');
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Process Event
        |--------------------------------------------------------------------------
        */

        $event = $payload['event'] ?? '';

        switch ($event) {

            case 'payment.captured':

                $payment = $payload['payload']['payment']['entity'] ?? [];

                $paymentId = $payment['id'] ?? null;
                $orderId = $payment['order_id'] ?? null;

                if (! $paymentId || ! $orderId) {
                    Log::warning('Missing payment_id or order_id.', $payment);

                    return;
                }

                $subscription = UserSubscription::where('payment_id', $orderId)->first();

                if (! $subscription) {
                    Log::warning("Subscription not found for order {$orderId}");

                    return;
                }

                $plan = $subscription->subscription;

                $subscription->update([
                    'payment_id' => $paymentId,
                    'payment_status' => 'completed',
                    'starts_at' => now(),
                    'ends_at' => now()->addDays($plan->duration_days),
                ]);

                SendSubscriptionConfirmation::dispatch(
                    $subscription->user,
                    $subscription
                );

                Log::info("Subscription activated for order {$orderId}");

                break;

            case 'payment.failed':

                $payment = $payload['payload']['payment']['entity'] ?? [];

                $orderId = $payment['order_id'] ?? null;

                if ($orderId) {

                    UserSubscription::where('payment_id', $orderId)
                        ->update([
                            'payment_status' => 'failed',
                        ]);

                    Log::info("Payment marked failed for order {$orderId}");
                }

                break;

            default:

                Log::info("Unhandled Razorpay webhook event: {$event}");
        }
    }

    /**
     * Check if a user has an active (paid) subscription.
     */
    public function isActive(User $user): bool
    {
        return $user->hasActiveSubscription();
    }
}
