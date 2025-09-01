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
        Schema::table('logbook_entries', function (Blueprint $table) {
            // Modify the status enum to include approval statuses
            $table->enum('status', ['not_started', 'logged_in', 'completed', 'approved', 'rejected'])
                  ->default('not_started')
                  ->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('logbook_entries', function (Blueprint $table) {
            // Revert back to original status enum
            $table->enum('status', ['not_started', 'logged_in', 'completed'])
                  ->default('not_started')
                  ->change();
        });
    }
};
