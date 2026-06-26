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
        Schema::table('users', function (Blueprint $table) {
            $table->string('email')->nullable()->change();
            $table->string('mobile', 20)->nullable()->unique()->change();
        });

        Schema::table('student_profiles', function (Blueprint $table) {
            $table->string('school_name')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('email')->nullable(false)->change();
            if (Schema::getConnection()->getDriverName() !== 'sqlite') {
                $table->dropUnique(['mobile']);
            }
        });

        Schema::table('student_profiles', function (Blueprint $table) {
            $table->string('school_name')->nullable(false)->change();
        });
    }
};
