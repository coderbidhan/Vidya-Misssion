<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->string('uuid', 64)->unique();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->nullableMorphs('payable');
            $table->string('purpose', 50)->default('subscription_purchase')->index();
            $table->string('gateway', 30)->default('razorpay');
            $table->string('gateway_order_id', 100)->nullable()->index();
            $table->string('gateway_payment_id', 100)->nullable()->index();
            $table->string('gateway_signature')->nullable();
            $table->unsignedBigInteger('amount')->comment('Amount in integer minor units (paise)');
            $table->string('currency', 10)->default('INR');
            $table->string('status', 30)->default('pending')->index();
            $table->string('gateway_status', 50)->nullable();
            $table->string('payment_method', 50)->nullable();
            $table->string('receipt', 100)->nullable()->index();
            $table->boolean('is_captured')->default(false);
            $table->timestamp('captured_at')->nullable();
            $table->timestamp('authorized_at')->nullable();
            $table->timestamp('failed_at')->nullable();
            $table->string('failure_code', 100)->nullable();
            $table->text('failure_reason')->nullable();
            $table->unsignedBigInteger('amount_refunded')->default(0)->comment('Total refunded in paise');
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index(['gateway', 'status']);
            $table->index(['user_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
