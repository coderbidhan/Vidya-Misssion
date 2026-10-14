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
        Schema::create('academic_classes', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('class_level')->unique();
            $table->string('name');
            $table->string('display_name')->nullable();
            $table->string('board')->default('WBBSE');
            $table->string('curriculum')->nullable();
            $table->string('stage')->nullable(); // Primary, Upper Primary, Secondary, Higher Secondary
            $table->string('stream')->nullable(); // General, Science, Arts, Commerce
            $table->string('icon')->nullable();
            $table->text('description')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Seed initial default classes 1 through 12
        $now = now();
        $defaultClasses = [
            ['class_level' => 1,  'name' => 'Class 1',  'display_name' => 'Class 1 (Standard 1)',  'board' => 'WBBSE', 'stage' => 'Primary', 'sort_order' => 1],
            ['class_level' => 2,  'name' => 'Class 2',  'display_name' => 'Class 2 (Standard 2)',  'board' => 'WBBSE', 'stage' => 'Primary', 'sort_order' => 2],
            ['class_level' => 3,  'name' => 'Class 3',  'display_name' => 'Class 3 (Standard 3)',  'board' => 'WBBSE', 'stage' => 'Primary', 'sort_order' => 3],
            ['class_level' => 4,  'name' => 'Class 4',  'display_name' => 'Class 4 (Standard 4)',  'board' => 'WBBSE', 'stage' => 'Primary', 'sort_order' => 4],
            ['class_level' => 5,  'name' => 'Class 5',  'display_name' => 'Class 5 (Standard 5)',  'board' => 'WBBSE', 'stage' => 'Upper Primary', 'sort_order' => 5],
            ['class_level' => 6,  'name' => 'Class 6',  'display_name' => 'Class 6 (Standard 6)',  'board' => 'WBBSE', 'stage' => 'Upper Primary', 'sort_order' => 6],
            ['class_level' => 7,  'name' => 'Class 7',  'display_name' => 'Class 7 (Standard 7)',  'board' => 'WBBSE', 'stage' => 'Upper Primary', 'sort_order' => 7],
            ['class_level' => 8,  'name' => 'Class 8',  'display_name' => 'Class 8 (Standard 8)',  'board' => 'WBBSE', 'stage' => 'Upper Primary', 'sort_order' => 8],
            ['class_level' => 9,  'name' => 'Class 9',  'display_name' => 'Class 9 (Standard 9)',  'board' => 'WBBSE', 'stage' => 'Secondary', 'sort_order' => 9],
            ['class_level' => 10, 'name' => 'Class 10', 'display_name' => 'Class 10 (Madhyamik)', 'board' => 'WBBSE', 'stage' => 'Secondary (Madhyamik)', 'sort_order' => 10],
            ['class_level' => 11, 'name' => 'Class 11', 'display_name' => 'Class 11 (Higher Secondary)', 'board' => 'WBCHSE', 'stage' => 'Higher Secondary', 'sort_order' => 11],
            ['class_level' => 12, 'name' => 'Class 12', 'display_name' => 'Class 12 (Higher Secondary)', 'board' => 'WBCHSE', 'stage' => 'Higher Secondary', 'sort_order' => 12],
        ];

        foreach ($defaultClasses as $c) {
            DB::table('academic_classes')->insert(array_merge($c, [
                'curriculum' => 'West Bengal Board (' . $c['board'] . ')',
                'stream' => in_array($c['class_level'], [11, 12]) ? 'Science / Arts / Commerce' : 'General',
                'icon' => '🏫',
                'description' => $c['display_name'] . ' curriculum under ' . $c['board'],
                'is_active' => $c['class_level'] <= 10,
                'created_at' => $now,
                'updated_at' => $now,
            ]));
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('academic_classes');
    }
};
