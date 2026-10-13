<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('webhook_events', function (Blueprint $table) {
            $table->id();
            $table->string('gateway', 30)->default('razorpay');
            $table->string('event_id', 150)->unique();
            $table->string('event_type', 80)->index();
            $table->json('payload');
            $table->string('status', 30)->default('received')->index();
            $table->text('error_message')->nullable();
            $table->timestamp('processed_at')->nullable();
            $table->timestamps();

            $table->index(['gateway', 'event_type', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('webhook_events');
    }
};
