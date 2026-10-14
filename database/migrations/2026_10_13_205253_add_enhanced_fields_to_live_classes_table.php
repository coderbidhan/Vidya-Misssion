<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('live_classes', function (Blueprint $table) {
            $table->unsignedInteger('class_level')->nullable()->after('id')->index();
            $table->foreignId('subject_id')->nullable()->after('class_level')->constrained('subjects')->nullOnDelete();
            $table->foreignId('chapter_id')->nullable()->change();
            $table->text('learning_objectives')->nullable()->after('description');
            $table->timestamp('ends_at')->nullable()->after('scheduled_at')->index();
            $table->string('timezone', 50)->default('Asia/Kolkata')->after('ends_at');
            $table->boolean('is_published')->default(true)->after('status')->index();
            $table->string('recording_url')->nullable()->after('is_published');
            $table->string('cancellation_reason')->nullable()->after('recording_url');
            $table->foreignId('created_by')->nullable()->after('cancellation_reason')->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->after('created_by')->constrained('users')->nullOnDelete();
        });

        // Backfill existing records from their associated chapter
        DB::statement('
            UPDATE live_classes lc
            INNER JOIN chapters c ON lc.chapter_id = c.id
            SET lc.class_level = c.class_level,
                lc.subject_id = c.subject_id,
                lc.ends_at = DATE_ADD(lc.scheduled_at, INTERVAL lc.duration_minutes MINUTE)
            WHERE lc.class_level IS NULL
        ');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('live_classes', function (Blueprint $table) {
            $table->dropForeign(['subject_id']);
            $table->dropForeign(['created_by']);
            $table->dropForeign(['updated_by']);
            $table->dropIndex(['class_level']);
            $table->dropIndex(['ends_at']);
            $table->dropIndex(['is_published']);
            $table->dropColumn([
                'class_level',
                'subject_id',
                'learning_objectives',
                'ends_at',
                'timezone',
                'is_published',
                'recording_url',
                'cancellation_reason',
                'created_by',
                'updated_by',
            ]);
        });
    }
};
