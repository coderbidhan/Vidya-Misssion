<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Services\Payment\PaymentService;
use App\Traits\ApiResponse;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    use ApiResponse;

    public function __construct(
        protected PaymentService $paymentService
    ) {}

    /**
     * Initiate a generic one-time payment.
     *
     * POST /api/payments/one-time
     */
    public function initiateOneTime(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|gt:0',
            'purpose' => 'nullable|string|max:50',
            'description' => 'nullable|string|max:255',
            'notes' => 'nullable|array',
        ]);

        $user = $request->user();
        $amountPaise = (int) round($validated['amount'] * 100);
        $purpose = $validated['purpose'] ?? 'one_time_purchase';
        $description = $validated['description'] ?? 'One-time payment';
        $notes = $validated['notes'] ?? [];

        try {
            $orderData = $this->paymentService->initiateOneTimePayment(
                $user,
                null,
                $amountPaise,
                $purpose,
                $description,
                $notes
            );

            return $this->successResponse($orderData, 'One-time payment order created.');
        } catch (Exception $e) {
            return $this->errorResponse($e->getMessage(), 500, 'PAYMENT_ERROR');
        }
    }

    /**
     * Verify payment signature from checkout.
     *
     * POST /api/payments/verify
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
            $payment = $this->paymentService->verifyAndCompletePayment(
                $user,
                $validated['razorpay_order_id'],
                $validated['razorpay_payment_id'],
                $validated['razorpay_signature']
            );

            return $this->successResponse($payment, 'Payment verified successfully.');
        } catch (Exception $e) {
            return $this->errorResponse($e->getMessage(), 400, 'VERIFICATION_FAILED');
        }
    }

    /**
     * Report payment checkout failure or dismissal.
     *
     * POST /api/payments/failed
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
            $payment = $this->paymentService->handlePaymentFailure(
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
     * Get details of a payment belonging to the authenticated user.
     *
     * GET /api/payments/{uuid}
     */
    public function show(Request $request, string $id): JsonResponse
    {
        $payment = Payment::where('user_id', $request->user()->id)
            ->where(function ($q) use ($id) {
                $q->where('id', $id)
                    ->orWhere('uuid', $id)
                    ->orWhere('gateway_order_id', $id);
            })
            ->with(['refunds'])
            ->firstOrFail();

        return $this->successResponse($payment);
    }
}
