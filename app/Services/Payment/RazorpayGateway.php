<?php

namespace App\Services\Payment;

use Exception;
use Illuminate\Support\Facades\Log;
use Razorpay\Api\Api as RazorpayApi;

class RazorpayGateway
{
    protected ?RazorpayApi $api = null;

    public function __construct(
        protected ?string $keyId = null,
        protected ?string $keySecret = null,
        protected ?string $webhookSecret = null
    ) {
        $this->keyId = $keyId ?? config('vidyamission.razorpay.key_id');
        $this->keySecret = $keySecret ?? config('vidyamission.razorpay.key_secret');
        $this->webhookSecret = $webhookSecret ?? config('vidyamission.razorpay.webhook_secret');
    }

    /**
     * Get or initialize the Razorpay API client.
     */
    public function getApi(): RazorpayApi
    {
        if ($this->api === null) {
            $this->api = new RazorpayApi($this->keyId, $this->keySecret);
        }

        return $this->api;
    }

    /**
     * Set a custom or mocked Razorpay API client instance (for testing).
     */
    public function setApi(RazorpayApi $api): self
    {
        $this->api = $api;

        return $this;
    }

    public function getKeyId(): string
    {
        return (string) $this->keyId;
    }

    /**
     * Create an authoritative Razorpay order.
     *
     * @param  int  $amountPaise  Amount in integer minor units (paise)
     * @param  string  $currency  Currency code (default INR)
     * @param  string  $receipt  Unique receipt identifier
     * @param  array  $notes  Arbitrary metadata notes
     * @return array Created order array
     *
     * @throws Exception
     */
    public function createOrder(int $amountPaise, string $currency, string $receipt, array $notes = []): array
    {
        $orderData = [
            'amount' => $amountPaise,
            'currency' => strtoupper($currency),
            'receipt' => $receipt,
            'payment_capture' => config('vidyamission.razorpay.auto_capture', true) ? 1 : 0,
            'notes' => $notes,
        ];

        try {
            $order = $this->getApi()->order->create($orderData);

            return $order->toArray();
        } catch (Exception $e) {
            Log::error('Razorpay order creation failed', [
                'receipt' => $receipt,
                'amount_paise' => $amountPaise,
                'error' => $e->getMessage(),
            ]);

            throw new Exception('Failed to create payment order with gateway: '.$e->getMessage(), 0, $e);
        }
    }

    /**
     * Verify checkout payment signature (order_id + payment_id signed with key_secret).
     */
    public function verifyPaymentSignature(string $orderId, string $paymentId, string $signature): bool
    {
        if (empty($orderId) || empty($paymentId) || empty($signature) || empty($this->keySecret)) {
            return false;
        }

        $expectedSignature = hash_hmac('sha256', $orderId.'|'.$paymentId, $this->keySecret);

        return hash_equals($expectedSignature, $signature);
    }

    /**
     * Verify incoming webhook signature using the configured webhook secret (with keySecret fallback) and raw body.
     */
    public function verifyWebhookSignature(string $rawPayload, string $signature): bool
    {
        if (empty($rawPayload) || empty($signature)) {
            return false;
        }

        $secrets = array_unique(array_filter([$this->webhookSecret, $this->keySecret]));

        foreach ($secrets as $secret) {
            if (hash_equals(hash_hmac('sha256', $rawPayload, $secret), $signature)) {
                return true;
            }
        }

        // Fallback: check normalized JSON representation for clients that re-encoded
        $decoded = json_decode($rawPayload, true);
        if (is_array($decoded)) {
            $normalized = json_encode($decoded);
            foreach ($secrets as $secret) {
                if (hash_equals(hash_hmac('sha256', $normalized, $secret), $signature)) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Fetch payment details from Razorpay.
     */
    public function fetchPayment(string $paymentId): array
    {
        try {
            $payment = $this->getApi()->payment->fetch($paymentId);

            return $payment->toArray();
        } catch (Exception $e) {
            Log::error('Razorpay fetch payment failed', [
                'payment_id' => $paymentId,
                'error' => $e->getMessage(),
            ]);

            throw new Exception('Failed to fetch payment details: '.$e->getMessage(), 0, $e);
        }
    }

    /**
     * Fetch order details from Razorpay.
     */
    public function fetchOrder(string $orderId): array
    {
        try {
            $order = $this->getApi()->order->fetch($orderId);

            return $order->toArray();
        } catch (Exception $e) {
            Log::error('Razorpay fetch order failed', [
                'order_id' => $orderId,
                'error' => $e->getMessage(),
            ]);

            throw new Exception('Failed to fetch order details: '.$e->getMessage(), 0, $e);
        }
    }

    /**
     * Fetch all payments attempted for a given order.
     */
    public function fetchOrderPayments(string $orderId): array
    {
        try {
            $payments = $this->getApi()->order->fetch($orderId)->payments();

            return $payments->toArray();
        } catch (Exception $e) {
            Log::error('Razorpay fetch order payments failed', [
                'order_id' => $orderId,
                'error' => $e->getMessage(),
            ]);

            throw new Exception('Failed to fetch order payments: '.$e->getMessage(), 0, $e);
        }
    }

    /**
     * Manually capture an authorized payment.
     */
    public function capturePayment(string $paymentId, int $amountPaise, string $currency = 'INR'): array
    {
        try {
            $payment = $this->getApi()->payment->fetch($paymentId);

            // If already captured, return payment details
            if (($payment['status'] ?? null) === 'captured') {
                return $payment->toArray();
            }

            $captured = $payment->capture([
                'amount' => $amountPaise,
                'currency' => strtoupper($currency),
            ]);

            return $captured->toArray();
        } catch (Exception $e) {
            Log::error('Razorpay payment capture failed', [
                'payment_id' => $paymentId,
                'amount_paise' => $amountPaise,
                'error' => $e->getMessage(),
            ]);

            throw new Exception('Failed to capture payment: '.$e->getMessage(), 0, $e);
        }
    }

    /**
     * Create a refund for a captured payment.
     */
    public function createRefund(string $paymentId, int $amountPaise, array $notes = []): array
    {
        try {
            $payment = $this->getApi()->payment->fetch($paymentId);
            $refund = $payment->refund([
                'amount' => $amountPaise,
                'notes' => $notes,
            ]);

            return $refund->toArray();
        } catch (Exception $e) {
            Log::error('Razorpay refund creation failed', [
                'payment_id' => $paymentId,
                'amount_paise' => $amountPaise,
                'error' => $e->getMessage(),
            ]);

            throw new Exception('Failed to process refund with gateway: '.$e->getMessage(), 0, $e);
        }
    }

    /**
     * Fetch refund status from Razorpay.
     */
    public function fetchRefund(string $paymentId, string $refundId): array
    {
        try {
            $refund = $this->getApi()->payment->fetch($paymentId)->fetchRefund($refundId);

            return $refund->toArray();
        } catch (Exception $e) {
            Log::error('Razorpay fetch refund failed', [
                'payment_id' => $paymentId,
                'refund_id' => $refundId,
                'error' => $e->getMessage(),
            ]);

            throw new Exception('Failed to fetch refund details: '.$e->getMessage(), 0, $e);
        }
    }
}
