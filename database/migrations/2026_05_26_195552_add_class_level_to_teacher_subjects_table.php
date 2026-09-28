<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('teacher_subjects', function (Blueprint $table) {
            // Drop foreign keys first to allow dropping the unique index in MySQL
            $table->dropForeign(['teacher_id']);
            $table->dropForeign(['subject_id']);

            // Drop old unique constraint
            $table->dropUnique(['teacher_id', 'subject_id']);

            // Add class_level
            $table->integer('class_level')->after('subject_id')->default(1);

            // Add new unique constraint
            $table->unique(['teacher_id', 'subject_id', 'class_level']);

            // Recreate foreign keys
            $table->foreign('teacher_id')->references('id')->on('users')->cascadeOnDelete();
            $table->foreign('subject_id')->references('id')->on('subjects')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('teacher_subjects', function (Blueprint $table) {
            $table->dropForeign(['teacher_id']);
            $table->dropForeign(['subject_id']);

            $table->dropUnique(['teacher_id', 'subject_id', 'class_level']);
            $table->dropColumn('class_level');

            $table->unique(['teacher_id', 'subject_id']);

            $table->foreign('teacher_id')->references('id')->on('users')->cascadeOnDelete();
            $table->foreign('subject_id')->references('id')->on('subjects')->cascadeOnDelete();
        });
    }
};
