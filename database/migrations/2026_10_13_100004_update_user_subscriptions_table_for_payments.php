<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('user_subscriptions', function (Blueprint $table) {
            $table->string('gateway_order_id', 100)->nullable()->index()->after('subscription_id');
            $table->foreignId('payment_record_id')->nullable()->constrained('payments')->nullOnDelete()->after('gateway_order_id');
        });
    }

    public function down(): void
    {
        Schema::table('user_subscriptions', function (Blueprint $table) {
            $table->dropForeign(['payment_record_id']);
            $table->dropColumn(['payment_record_id', 'gateway_order_id']);
        });
    }
};
