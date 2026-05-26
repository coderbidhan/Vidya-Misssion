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
     * @return array  Gateway-specific order data to return to the client.
     * @throws \Exception
     */
    public function initiatePayment(User $user, Subscription $plan): array
    {
        $gateway = config('vidyamission.payment_gateway', 'razorpay');

        // Create a pending subscription record
        $userSubscription = UserSubscription::create([
            'user_id'          => $user->id,
            'subscription_id'  => $plan->id,
            'payment_gateway'  => $gateway,
            'payment_status'   => 'pending',
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
            'amount'          => (int) ($plan->price * 100), // Razorpay uses paise
            'currency'        => 'INR',
            'receipt'         => 'sub_' . $userSubscription->id,
            'notes'           => [
                'subscription_id'      => $plan->id,
                'user_subscription_id' => $userSubscription->id,
                'plan_name'            => $plan->name,
            ],
        ];

        $order = $api->order->create($orderData);

        $userSubscription->update(['payment_id' => $order->id]);

        return [
            'order_id'            => $order->id,
            'user_subscription_id' => $userSubscription->id,
            'amount'              => $plan->price,
            'currency'            => 'INR',
            'key_id'              => config('vidyamission.razorpay.key_id'),
            'plan_name'           => $plan->name,
        ];
    }

    /**
     * Handle an incoming payment gateway webhook payload.
     *
     * Verifies the HMAC signature and updates the subscription status.
     *
     * @throws \Exception on signature verification failure
     */
    public function handleWebhook(array $payload, string $signature, string $gateway): void
    {
        if ($gateway === 'razorpay') {
            $this->handleRazorpayWebhook($payload, $signature);
            return;
        }

        throw new \Exception("Unsupported gateway webhook: {$gateway}");
    }

    /**
     * Handle Razorpay-specific webhook verification and processing.
     */
    private function handleRazorpayWebhook(array $payload, string $signature): void
    {
        $secret = config('vidyamission.razorpay.key_secret');

        // Verify HMAC signature
        $expectedSignature = hash_hmac('sha256', json_encode($payload), $secret);
        if (! hash_equals($expectedSignature, $signature)) {
            throw new \Exception('Invalid Razorpay webhook signature.');
        }

        $event = $payload['event'] ?? null;

        if ($event === 'payment.captured') {
            $paymentId = $payload['payload']['payment']['entity']['id'] ?? null;
            $orderId   = $payload['payload']['payment']['entity']['order_id'] ?? null;

            if (! $paymentId || ! $orderId) {
                return;
            }

            $userSubscription = UserSubscription::where('payment_id', $orderId)->first();

            if (! $userSubscription) {
                Log::warning("Razorpay webhook: no UserSubscription found for order {$orderId}");
                return;
            }

            $plan = $userSubscription->subscription;

            $userSubscription->update([
                'payment_id'     => $paymentId,
                'payment_status' => 'completed',
                'starts_at'      => now(),
                'ends_at'        => now()->addDays($plan->duration_days),
            ]);

            // Dispatch confirmation email
            SendSubscriptionConfirmation::dispatch($userSubscription->user, $userSubscription);
        }

        if ($event === 'payment.failed') {
            $orderId = $payload['payload']['payment']['entity']['order_id'] ?? null;
            if ($orderId) {
                UserSubscription::where('payment_id', $orderId)
                    ->update(['payment_status' => 'failed']);
            }
        }
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
