<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('chapters', function (Blueprint $table) {
            $table->string('image')->nullable()->after('title');
            $table->string('color', 50)->nullable()->after('image');
            $table->string('theme', 50)->nullable()->after('color');
        });
    }

    public function down(): void
    {
        Schema::table('chapters', function (Blueprint $table) {
            $table->dropColumn(['image', 'color', 'theme']);
        });
    }
};
