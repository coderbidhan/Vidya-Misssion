<?php

namespace App\Services\Payment;

use App\Models\Payment;
use App\Models\Refund;
use App\Models\User;
use App\Models\UserSubscription;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class RefundService
{
    public function __construct(
        protected RazorpayGateway $gateway
    ) {}

    /**
     * Initiate a full or partial refund for a captured payment.
     *
     * @param  Payment  $payment  The payment to refund
     * @param  int  $amountPaise  Refund amount in integer paise
     * @param  string|null  $reason  Admin refund reason
     * @param  User|null  $initiator  Admin user initiating the refund
     *
     * @throws Exception
     */
    public function initiateRefund(
        Payment $payment,
        int $amountPaise,
        ?string $reason = null,
        ?User $initiator = null
    ): Refund {
        if ($amountPaise <= 0) {
            throw new Exception('Refund amount must be greater than zero.');
        }

        if (! $payment->isCaptured()) {
            throw new Exception('Only captured payments can be refunded.');
        }

        if (empty($payment->gateway_payment_id)) {
            throw new Exception('Payment does not have a valid gateway payment identifier.');
        }

        return DB::transaction(function () use ($payment, $amountPaise, $reason, $initiator) {
            // Lock payment row to prevent race conditions in concurrent refund requests
            $lockedPayment = Payment::where('id', $payment->id)
                ->lockForUpdate()
                ->firstOrFail();

            $refundableBalance = $lockedPayment->refundable_balance;

            if ($amountPaise > $refundableBalance) {
                $maxRupees = round($refundableBalance / 100, 2);
                throw new Exception("Refund amount exceeds available refundable balance of ₹{$maxRupees}.");
            }

            // 1. Create internal pending refund record
            $refund = Refund::create([
                'payment_id' => $lockedPayment->id,
                'user_id' => $lockedPayment->user_id,
                'amount' => $amountPaise,
                'currency' => $lockedPayment->currency,
                'status' => 'pending',
                'reason' => $reason,
                'initiated_by' => $initiator?->id,
            ]);

            try {
                // 2. Execute refund via Razorpay API
                $notes = array_filter([
                    'refund_id' => (string) $refund->id,
                    'payment_uuid' => $lockedPayment->uuid,
                    'reason' => $reason,
                ]);

                $gatewayRefund = $this->gateway->createRefund(
                    $lockedPayment->gateway_payment_id,
                    $amountPaise,
                    $notes
                );

                $gatewayRefundId = $gatewayRefund['id'] ?? null;
                $gatewayStatus = $gatewayRefund['status'] ?? 'processed';

                // 3. Update refund record
                $isProcessed = in_array($gatewayStatus, ['processed', 'success']);
                $refund->update([
                    'gateway_refund_id' => $gatewayRefundId,
                    'status' => $isProcessed ? 'processed' : 'pending',
                    'processed_at' => $isProcessed ? now() : null,
                    'metadata' => $gatewayRefund,
                ]);

                // 4. Update payment amount_refunded and status
                $newRefundedTotal = $lockedPayment->amount_refunded + $amountPaise;
                $paymentStatus = $newRefundedTotal >= $lockedPayment->amount
                    ? 'refunded'
                    : 'partially_refunded';

                $lockedPayment->update([
                    'amount_refunded' => $newRefundedTotal,
                    'status' => $paymentStatus,
                ]);

                // 5. If fully refunded and attached to UserSubscription, update subscription
                if ($paymentStatus === 'refunded' && $lockedPayment->payable_type === UserSubscription::class) {
                    UserSubscription::where('id', $lockedPayment->payable_id)
                        ->update(['payment_status' => 'refunded']);
                }

                return $refund->fresh();
            } catch (Exception $e) {
                $refund->update([
                    'status' => 'failed',
                    'failed_at' => now(),
                    'failure_reason' => $e->getMessage(),
                ]);

                Log::error('Refund execution failed', [
                    'payment_id' => $lockedPayment->id,
                    'amount_paise' => $amountPaise,
                    'error' => $e->getMessage(),
                ]);

                throw $e;
            }
        });
    }

    /**
     * Reconcile an individual refund with live gateway status.
     *
     * @throws Exception
     */
    public function reconcileRefund(Refund $refund): Refund
    {
        if (! $refund->gateway_refund_id || ! $refund->payment?->gateway_payment_id) {
            return $refund;
        }

        try {
            $gatewayRefund = $this->gateway->fetchRefund(
                $refund->payment->gateway_payment_id,
                $refund->gateway_refund_id
            );

            $status = $gatewayRefund['status'] ?? null;

            if ($status === 'processed' && $refund->status !== 'processed') {
                $refund->update([
                    'status' => 'processed',
                    'processed_at' => now(),
                    'metadata' => $gatewayRefund,
                ]);
            } elseif ($status === 'failed' && $refund->status !== 'failed') {
                $refund->update([
                    'status' => 'failed',
                    'failed_at' => now(),
                    'failure_reason' => $gatewayRefund['error_description'] ?? 'Refund failed.',
                    'metadata' => $gatewayRefund,
                ]);
            }

            return $refund->fresh();
        } catch (Exception $e) {
            Log::error('Refund reconciliation error', [
                'refund_id' => $refund->id,
                'error' => $e->getMessage(),
            ]);

            throw $e;
        }
    }
}
