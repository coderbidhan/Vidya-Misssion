<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('recorded_classes', function (Blueprint $table) {
            if (! Schema::hasColumn('recorded_classes', 'video_url')) {
                $table->string('video_url', 500)->nullable()->after('description');
            }

            // Ensure legacy gdrive columns are nullable to support pure YouTube URLs
            $table->string('gdrive_file_id')->nullable()->change();
            $table->string('gdrive_embed_url')->nullable()->change();
        });

        // Safe backfill for existing records that have an 11-character YouTube video ID in gdrive_file_id
        DB::table('recorded_classes')
            ->whereNull('video_url')
            ->whereNotNull('gdrive_file_id')
            ->where('gdrive_file_id', 'regexp', '^[a-zA-Z0-9_-]{11}$')
            ->update([
                'video_url' => DB::raw("CONCAT('https://www.youtube.com/watch?v=', gdrive_file_id)"),
            ]);
    }

    public function down(): void
    {
        Schema::table('recorded_classes', function (Blueprint $table) {
            if (Schema::hasColumn('recorded_classes', 'video_url')) {
                $table->dropColumn('video_url');
            }
        });
    }
};
