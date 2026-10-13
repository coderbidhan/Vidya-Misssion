<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Subscription;
use App\Services\SubscriptionService;
use App\Traits\ApiResponse;
use Exception;
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
            ->with(['subscription', 'paymentRecord'])
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
        } catch (Exception $e) {
            return $this->errorResponse($e->getMessage(), 500, 'PAYMENT_ERROR');
        }
    }

    /**
     * Verify payment signature returned by Razorpay Checkout.
     *
     * POST /api/subscriptions/verify
     */
    public function verify(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'razorpay_order_id' => 'required|string',
            'razorpay_payment_id' => 'required|string',
            'razorpay_signature' => 'required|string',
        ]);

        $user = $request->user();

        try {
            $payment = $this->subscriptionService->verifyPayment(
                $user,
                $validated['razorpay_order_id'],
                $validated['razorpay_payment_id'],
                $validated['razorpay_signature']
            );

            $activeSubscription = $user->userSubscriptions()
                ->with('subscription')
                ->where('payment_status', 'completed')
                ->orderByDesc('ends_at')
                ->first();

            return $this->successResponse([
                'payment' => $payment,
                'subscription' => $activeSubscription,
            ], 'Payment verified and subscription activated successfully.');
        } catch (Exception $e) {
            return $this->errorResponse($e->getMessage(), 400, 'VERIFICATION_FAILED');
        }
    }

    /**
     * Report payment checkout failure or dismissal.
     *
     * POST /api/subscriptions/failed
     */
    public function failed(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'razorpay_order_id' => 'required|string',
            'error_code' => 'nullable|string',
            'error_description' => 'nullable|string',
        ]);

        $user = $request->user();

        try {
            $payment = $this->subscriptionService->handleFailure(
                $user,
                $validated['razorpay_order_id'],
                $validated['error_code'] ?? null,
                $validated['error_description'] ?? null
            );

            return $this->successResponse($payment, 'Payment failure recorded.');
        } catch (Exception $e) {
            return $this->errorResponse($e->getMessage(), 400, 'PAYMENT_ERROR');
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
        $gateway = $request->header('X-Payment-Gateway', config('vidyamission.payment_gateway', 'razorpay'));
        $signature = (string) $request->header('X-Razorpay-Signature', $request->header('X-Webhook-Signature', ''));

        if (empty($signature)) {
            return $this->errorResponse('Missing webhook signature.', 400, 'INVALID_SIGNATURE');
        }

        $rawPayload = $request->getContent();

        try {
            $this->subscriptionService->handleWebhook($rawPayload, $signature, $gateway);

            return $this->successResponse(null, 'Webhook processed.');
        } catch (Exception $e) {
            return $this->errorResponse($e->getMessage(), 400, 'WEBHOOK_ERROR');
        }
    }
}
