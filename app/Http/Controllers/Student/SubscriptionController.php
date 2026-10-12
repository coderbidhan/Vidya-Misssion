<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Subscription;
use App\Services\SubscriptionService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SubscriptionController extends Controller
{
    use ApiResponse;

    public function __construct(private readonly SubscriptionService $subscriptionService) {}

    /**
     * List all active subscription plans.
     *
     * GET /api/subscriptions
     */
    public function plans(): JsonResponse
    {
        $plans = Subscription::where('is_active', true)->orderBy('price')->get();

        return $this->successResponse($plans);
    }

    /**
     * Get the current user's active subscription.
     *
     * GET /api/subscriptions/current
     */
    public function current(Request $request): JsonResponse
    {
        $subscription = $request->user()
            ->userSubscriptions()
            ->with('subscription')
            ->where('payment_status', 'completed')
            ->where('ends_at', '>=', now())
            ->orderByDesc('ends_at')
            ->first();

        return $this->successResponse($subscription);
    }

    /**
     * Initiate a payment order for a subscription plan.
     *
     * POST /api/subscriptions/{id}/initiate
     */
    public function initiate(Request $request, int $id): JsonResponse
    {
        $plan = Subscription::where('is_active', true)->findOrFail($id);
        $user = $request->user();

        try {
            $orderData = $this->subscriptionService->initiatePayment($user, $plan);

            return $this->successResponse($orderData, 'Payment order created.');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500, 'PAYMENT_ERROR');
        }
    }

    /**
     * Handle payment gateway webhook.
     * This endpoint is NOT rate-limited or auth-guarded — it uses HMAC signature verification.
     *
     * POST /api/subscriptions/webhook
     */
    public function webhook(Request $request): JsonResponse
    {
        $gateway = $request->header('X-Payment-Gateway', config('vidyamission.payment_gateway'));
        $signature = $request->header('X-Razorpay-Signature', $request->header('X-Webhook-Signature', ''));
        $payload = $request->all();

        try {
            $this->subscriptionService->handleWebhook($payload, $signature, $gateway);

            return $this->successResponse(null, 'Webhook processed.');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 400, 'WEBHOOK_ERROR');
        }
    }
}
