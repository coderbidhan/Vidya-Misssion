<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * A chapter is tied to both a Subject AND a Class level.
     * e.g. Bengali (Subject) → Class 9 → Chapter 1: "Abol Tabol"
     *      Bengali (Subject) → Class 10 → Chapter 1: "Kobita Sangkalan"
     *
     * class_level: 1–12 (WB Board classes), or NULL = applies to all classes.
     */
    public function up(): void
    {
        Schema::table('chapters', function (Blueprint $table) {
            // After subject_id so the index is logical
            $table->tinyInteger('class_level')
                  ->nullable()
                  ->after('subject_id')
                  ->comment('School grade: 1–12. NULL = class-agnostic content.');

            // Composite index for fast lookups: subject × class
            $table->index(['subject_id', 'class_level'], 'chapters_subject_class_idx');
        });
    }

    public function down(): void
    {
        Schema::table('chapters', function (Blueprint $table) {
            $table->dropIndex('chapters_subject_class_idx');
            $table->dropColumn('class_level');
        });
    }
};
