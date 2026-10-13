<?php

namespace App\Console\Commands;

use App\Models\Payment;
use App\Models\Refund;
use App\Services\Payment\PaymentService;
use App\Services\Payment\RefundService;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class ReconcilePaymentsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'payment:reconcile
                            {--minutes=15 : Process transactions pending longer than this threshold in minutes}
                            {--limit=50 : Maximum number of transactions to reconcile per run}
                            {--dry-run : Report inconsistent transactions without applying database changes}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reconcile pending Razorpay payments and refunds against live gateway state';

    public function handle(PaymentService $paymentService, RefundService $refundService): int
    {
        $minutes = (int) $this->option('minutes');
        $limit = (int) $this->option('limit');
        $isDryRun = (bool) $this->option('dry-run');

        $this->info("Scanning transactions pending longer than {$minutes} minutes (limit: {$limit}, dry-run: ".($isDryRun ? 'YES' : 'NO').')...');

        // 1. Find pending payments with gateway order IDs
        $pendingPayments = Payment::where('status', 'pending')
            ->whereNotNull('gateway_order_id')
            ->where('created_at', '<=', now()->subMinutes($minutes))
            ->orderBy('created_at')
            ->limit($limit)
            ->get();

        $this->info("Found {$pendingPayments->count()} pending payments requiring verification.");

        $paymentResults = [];
        $capturedCount = 0;
        $failedCount = 0;
        $unchangedCount = 0;

        foreach ($pendingPayments as $payment) {
            $orderId = $payment->gateway_order_id;

            if ($isDryRun) {
                try {
                    $orderPayments = $paymentService->getGateway()->fetchOrderPayments($orderId);
                    $items = $orderPayments['items'] ?? [];
                    $hasCaptured = collect($items)->contains(fn ($item) => ($item['status'] ?? '') === 'captured');

                    $paymentResults[] = [
                        $payment->id,
                        $orderId,
                        $hasCaptured ? 'Would Capture & Fulfill' : 'Still Pending/Failed',
                        'Dry-run inspected',
                    ];
                } catch (Exception $e) {
                    $paymentResults[] = [$payment->id, $orderId, 'Error', $e->getMessage()];
                }

                continue;
            }

            try {
                $originalStatus = $payment->status;
                $reconciled = $paymentService->reconcilePayment($payment);

                if ($reconciled->isCaptured()) {
                    $capturedCount++;
                    $action = 'Captured & Fulfilled';
                } elseif ($reconciled->isFailed()) {
                    $failedCount++;
                    $action = 'Marked Failed / Expired';
                } else {
                    $unchangedCount++;
                    $action = 'Unchanged (Pending at Gateway)';
                }

                $paymentResults[] = [$payment->id, $orderId, $action, 'OK'];
            } catch (Exception $e) {
                Log::error('Reconciliation command error for payment', [
                    'payment_id' => $payment->id,
                    'error' => $e->getMessage(),
                ]);
                $paymentResults[] = [$payment->id, $orderId, 'Error', $e->getMessage()];
            }
        }

        if (! empty($paymentResults)) {
            $this->table(['Payment ID', 'Order ID', 'Outcome', 'Notes'], $paymentResults);
        }

        // 2. Reconcile pending refunds
        $pendingRefunds = Refund::where('status', 'pending')
            ->whereNotNull('gateway_refund_id')
            ->where('created_at', '<=', now()->subMinutes($minutes))
            ->limit($limit)
            ->get();

        $this->info("Found {$pendingRefunds->count()} pending refunds requiring verification.");

        $refundResults = [];
        foreach ($pendingRefunds as $refund) {
            if ($isDryRun) {
                $refundResults[] = [$refund->id, $refund->gateway_refund_id, 'Dry-run inspection', 'N/A'];

                continue;
            }

            try {
                $synced = $refundService->reconcileRefund($refund);
                $refundResults[] = [$refund->id, $refund->gateway_refund_id, $synced->status, 'Reconciled'];
            } catch (Exception $e) {
                $refundResults[] = [$refund->id, $refund->gateway_refund_id, 'Error', $e->getMessage()];
            }
        }

        if (! empty($refundResults)) {
            $this->table(['Refund ID', 'Gateway Refund ID', 'Status', 'Notes'], $refundResults);
        }

        $this->info("Reconciliation complete. Captured: {$capturedCount}, Failed: {$failedCount}, Unchanged: {$unchangedCount}.");

        return Command::SUCCESS;
    }
}
