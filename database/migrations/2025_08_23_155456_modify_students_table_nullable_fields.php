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
        Schema::table('students', function (Blueprint $table) {
            $table->string('program')->nullable()->change();
            $table->string('year')->nullable()->change();
            $table->string('coordinator_assigned')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->string('program')->nullable(false)->change();
            $table->string('year')->nullable(false)->change();
            $table->string('coordinator_assigned')->nullable(false)->change();
        });
    }
};
