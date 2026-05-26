<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Maps which subjects are taught in which school class level.
     * e.g. Bengali (subject_id=1) is taught in class_levels 5,6,7,8,9,10,11,12.
     * Math (subject_id=4) is taught in all classes 1-12.
     *
     * This is the second tier: Class → Subject → Chapter
     */
    public function up(): void
    {
        Schema::create('class_subjects', function (Blueprint $table) {
            $table->id();
            $table->tinyInteger('class_level')->unsigned()->comment('School grade 1–12');
            $table->foreignId('subject_id')->constrained()->onDelete('cascade');
            $table->timestamps();

            // A subject can only appear once per class
            $table->unique(['class_level', 'subject_id'], 'class_subject_unique');
            $table->index('class_level', 'class_subjects_class_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('class_subjects');
    }
};
