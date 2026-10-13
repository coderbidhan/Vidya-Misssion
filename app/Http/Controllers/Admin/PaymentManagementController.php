<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\Refund;
use App\Models\WebhookEvent;
use App\Services\Payment\PaymentService;
use App\Services\Payment\RefundService;
use App\Traits\ApiResponse;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PaymentManagementController extends Controller
{
    use ApiResponse;

    public function __construct(
        protected PaymentService $paymentService,
        protected RefundService $refundService
    ) {}

    /**
     * Dashboard operational metrics for payments and revenue.
     *
     * GET /api/admin/payments/dashboard
     */
    public function dashboard(Request $request): JsonResponse
    {
        $fromDate = $request->input('from_date');
        $toDate = $request->input('to_date');

        $query = Payment::query();
        if ($fromDate) {
            $query->whereDate('created_at', '>=', $fromDate);
        }
        if ($toDate) {
            $query->whereDate('created_at', '<=', $toDate);
        }

        // Aggregate statistics using exact integer paise
        $totalTransactions = (clone $query)->count();
        $capturedCount = (clone $query)->where('status', 'captured')->count();
        $pendingCount = (clone $query)->where('status', 'pending')->count();
        $failedCount = (clone $query)->where('status', 'failed')->count();
        $refundedCount = (clone $query)->whereIn('status', ['refunded', 'partially_refunded'])->count();

        $totalCapturedPaise = (int) (clone $query)->whereIn('status', ['captured', 'partially_refunded', 'refunded'])->sum('amount');
        $totalRefundedPaise = (int) (clone $query)->sum('amount_refunded');
        $netCollectedPaise = max(0, $totalCapturedPaise - $totalRefundedPaise);

        // Revenue by purpose
        $subPurchasePaise = (int) (clone $query)->where('purpose', 'subscription_purchase')
            ->whereIn('status', ['captured', 'partially_refunded', 'refunded'])
            ->sum('amount');

        $subRenewalPaise = (int) (clone $query)->where('purpose', 'subscription_renewal')
            ->whereIn('status', ['captured', 'partially_refunded', 'refunded'])
            ->sum('amount');

        $oneTimePaise = (int) (clone $query)->where('purpose', 'one_time_purchase')
            ->whereIn('status', ['captured', 'partially_refunded', 'refunded'])
            ->sum('amount');

        // Pending refund counts
        $pendingRefundCount = Refund::where('status', 'pending')->count();

        // Recent 5 transactions
        $recentPayments = Payment::with(['user:id,name,email'])
            ->orderByDesc('created_at')
            ->limit(5)
            ->get();

        return $this->successResponse([
            'counts' => [
                'total' => $totalTransactions,
                'captured' => $capturedCount,
                'pending' => $pendingCount,
                'failed' => $failedCount,
                'refunded' => $refundedCount,
                'pending_refunds' => $pendingRefundCount,
            ],
            'revenue' => [
                'captured_paise' => $totalCapturedPaise,
                'captured_rupees' => round($totalCapturedPaise / 100, 2),
                'refunded_paise' => $totalRefundedPaise,
                'refunded_rupees' => round($totalRefundedPaise / 100, 2),
                'net_paise' => $netCollectedPaise,
                'net_rupees' => round($netCollectedPaise / 100, 2),
                'currency' => 'INR',
            ],
            'by_purpose' => [
                'subscription_purchase_rupees' => round($subPurchasePaise / 100, 2),
                'subscription_renewal_rupees' => round($subRenewalPaise / 100, 2),
                'one_time_purchase_rupees' => round($oneTimePaise / 100, 2),
            ],
            'recent_transactions' => $recentPayments,
        ]);
    }

    /**
     * Searchable, filterable list of payments.
     *
     * GET /api/admin/payments
     */
    public function index(Request $request): JsonResponse
    {
        $status = $request->input('status');
        $purpose = $request->input('purpose');
        $search = $request->input('search');
        $fromDate = $request->input('from_date');
        $toDate = $request->input('to_date');

        $query = Payment::with(['user:id,name,email,mobile', 'refunds'])
            ->when($status, fn ($q) => $q->where('status', $status))
            ->when($purpose, fn ($q) => $q->where('purpose', $purpose))
            ->when($fromDate, fn ($q) => $q->whereDate('created_at', '>=', $fromDate))
            ->when($toDate, fn ($q) => $q->whereDate('created_at', '<=', $toDate))
            ->when($search, function ($q) use ($search) {
                $q->where(function ($sub) use ($search) {
                    $sub->where('gateway_order_id', 'like', "%{$search}%")
                        ->orWhere('gateway_payment_id', 'like', "%{$search}%")
                        ->orWhere('receipt', 'like', "%{$search}%")
                        ->orWhere('uuid', 'like', "%{$search}%")
                        ->orWhereHas('user', function ($uq) use ($search) {
                            $uq->where('name', 'like', "%{$search}%")
                                ->orWhere('email', 'like', "%{$search}%")
                                ->orWhere('mobile', 'like', "%{$search}%");
                        });
                });
            })
            ->orderByDesc('created_at');

        return $this->paginatedResponse($query->paginate(20));
    }

    /**
     * Get detailed transaction information.
     *
     * GET /api/admin/payments/{id}
     */
    public function show(int $id): JsonResponse
    {
        $payment = Payment::with([
            'user:id,name,email,mobile',
            'refunds.initiator:id,name,email',
            'payable',
            'userSubscription.subscription',
        ])->findOrFail($id);

        // Fetch related webhook events for audit trail
        $orderId = $payment->gateway_order_id;
        $paymentId = $payment->gateway_payment_id;

        $webhookEvents = WebhookEvent::where(function ($q) use ($orderId, $paymentId) {
            if ($orderId) {
                $q->where('payload', 'like', "%{$orderId}%");
            }
            if ($paymentId) {
                $q->orWhere('payload', 'like', "%{$paymentId}%");
            }
        })
            ->select(['id', 'event_id', 'event_type', 'status', 'error_message', 'processed_at', 'created_at'])
            ->orderByDesc('created_at')
            ->limit(20)
            ->get();

        return $this->successResponse([
            'payment' => $payment,
            'webhook_events' => $webhookEvents,
        ]);
    }

    /**
     * Issue a full or partial refund.
     *
     * POST /api/admin/payments/{id}/refund
     */
    public function refund(Request $request, int $id): JsonResponse
    {
        $request->validate([
            'amount' => 'required|numeric|gt:0',
            'reason' => 'nullable|string|max:255',
        ]);

        $payment = Payment::findOrFail($id);
        $amountPaise = (int) round($request->amount * 100);

        try {
            $refund = $this->refundService->initiateRefund(
                $payment,
                $amountPaise,
                $request->reason,
                $request->user()
            );

            return $this->successResponse([
                'refund' => $refund,
                'payment' => $payment->fresh(['refunds']),
            ], 'Refund initiated successfully.');
        } catch (Exception $e) {
            return $this->errorResponse($e->getMessage(), 400, 'REFUND_ERROR');
        }
    }

    /**
     * Live sync/reconcile payment status with Razorpay API.
     *
     * POST /api/admin/payments/{id}/reconcile
     */
    public function reconcile(int $id): JsonResponse
    {
        $payment = Payment::findOrFail($id);

        try {
            $synced = $this->paymentService->reconcilePayment($payment);

            return $this->successResponse($synced->load(['user', 'refunds']), 'Payment status reconciled with gateway.');
        } catch (Exception $e) {
            return $this->errorResponse($e->getMessage(), 400, 'RECONCILE_ERROR');
        }
    }

    /**
     * Audit log of webhook events.
     *
     * GET /api/admin/payments/webhook-events
     */
    public function webhookEvents(Request $request): JsonResponse
    {
        $status = $request->input('status');
        $eventType = $request->input('event_type');

        $query = WebhookEvent::query()
            ->when($status, fn ($q) => $q->where('status', $status))
            ->when($eventType, fn ($q) => $q->where('event_type', $eventType))
            ->orderByDesc('created_at');

        return $this->paginatedResponse($query->paginate(25));
    }
}
