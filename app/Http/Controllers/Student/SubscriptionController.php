<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Subscription;
use App\Services\SubscriptionService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

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
        $subscription = $request->user()->activeSubscription();

        return $this->successResponse($subscription);
    }

    /**
     * Get all subscription purchase history for the authenticated student.
     *
     * GET /api/subscriptions/history
     */
    public function history(Request $request): JsonResponse
    {
        $subscriptions = $request->user()
            ->userSubscriptions()
            ->with('subscription:id,name,price,duration_days,description')
            ->orderByDesc('created_at')
            ->get()
            ->map(fn ($sub) => [
                'id' => $sub->id,
                'plan_name' => $sub->subscription?->name ?? 'Custom Plan',
                'amount_paid' => $sub->subscription?->price ?? 0,
                'duration_days' => $sub->subscription?->duration_days ?? 0,
                'payment_gateway' => $sub->payment_gateway,
                'payment_id' => $sub->payment_id,
                'payment_status' => $sub->payment_status,
                'starts_at' => $sub->starts_at?->toIso8601String(),
                'ends_at' => $sub->ends_at?->toIso8601String(),
                'is_active' => $sub->isActive(),
                'days_remaining' => $sub->daysRemaining(),
                'created_at' => $sub->created_at?->toIso8601String(),
            ]);

        $totalSpent = $subscriptions->where('payment_status', 'completed')->sum('amount_paid');

        return $this->successResponse([
            'total_spent' => $totalSpent,
            'subscriptions' => $subscriptions,
        ]);
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
    // public function webhook(Request $request): JsonResponse
    // {
    //     $gateway   = $request->header('X-Payment-Gateway', config('vidyamission.payment_gateway'));
    //     $signature = $request->header('X-Razorpay-Signature', $request->header('X-Webhook-Signature', 'hdhnjdhkjdhfdhfjfj'));
    //     $payload   = $request->all();

    //     try {
    //         $this->subscriptionService->handleWebhook($payload, $signature, $gateway);

    //         return $this->successResponse(null, 'Webhook processed.');
    //     } catch (\Exception $e) {
    //         return $this->errorResponse($e->getMessage(), 400, 'WEBHOOK_ERROR');
    //     }
    // }
    public function webhook(Request $request): JsonResponse
    {
        $gateway = $request->header(
            'X-Payment-Gateway',
            config('vidyamission.payment_gateway')
        );

        $signature = $request->header('X-Razorpay-Signature', '');

        $payload = $request->all();

        $rawBody = $request->getContent();

        try {

            $this->subscriptionService->handleWebhook(
                $payload,
                $signature,
                $gateway,
                $rawBody
            );

            return $this->successResponse(null, 'Webhook processed.');

        } catch (\Throwable $e) {

            Log::error('Webhook processing failed.', [
                'message' => $e->getMessage(),
                'gateway' => $gateway,
                'received_signature' => $signature,
                'payload' => $payload,
                'raw_body' => $rawBody,
                'trace' => $e->getTraceAsString(),
            ]);

            return $this->errorResponse(
                $e->getMessage(),
                400,
                'WEBHOOK_ERROR'
            );
        }
    }
}
