<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Alter reward_type to varchar(50) to support video_watch_complete, live_class_attendance, etc.
        DB::statement('ALTER TABLE `rewards` MODIFY COLUMN `reward_type` VARCHAR(50) NOT NULL');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE `rewards` MODIFY COLUMN `reward_type` ENUM('chapter_complete', 'test_pass', 'streak') NOT NULL");
    }
};
