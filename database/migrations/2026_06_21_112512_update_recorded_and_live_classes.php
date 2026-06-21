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
        // 1. Update recorded_classes table
        Schema::table('recorded_classes', function (Blueprint $table) {
            $table->string('gdrive_file_id')->nullable()->change();
            $table->string('gdrive_embed_url')->nullable()->change();
            $table->string('video_type')->default('google_drive')->after('teacher_id');
            $table->text('video_url')->nullable()->after('video_type');
        });

        // Backfill video_url for existing records
        DB::table('recorded_classes')->whereNotNull('gdrive_file_id')->update([
            'video_url' => DB::raw("CONCAT('https://drive.google.com/file/d/', gdrive_file_id, '/view')"),
        ]);

        // 2. Update live_classes table
        Schema::table('live_classes', function (Blueprint $table) {
            $table->string('platform')->default('google_meet')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('recorded_classes', function (Blueprint $table) {
            $table->string('gdrive_file_id')->nullable(false)->change();
            $table->string('gdrive_embed_url')->nullable(false)->change();
            $table->dropColumn(['video_type', 'video_url']);
        });

        Schema::table('live_classes', function (Blueprint $table) {
            // Note: going back to enum can fail if there are youtube/google_drive values in DB,
            // so we keep it as string or change it back if needed.
            // For safety we don't force a rollback to enum if values exist.
        });
    }
};
