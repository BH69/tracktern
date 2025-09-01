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
        Schema::create('logbook_entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('user_name'); // Store the user's name for easier retrieval
            $table->date('log_date');
            $table->time('time_in')->nullable();
            $table->time('time_out')->nullable();
            $table->decimal('hours_logged', 5, 2)->default(0);
            $table->enum('status', ['not_started', 'logged_in', 'completed'])->default('not_started');
            $table->text('notes')->nullable();
            $table->timestamps();
            
            // Ensure one entry per user per day
            $table->unique(['user_id', 'log_date']);
            
            // Index for better query performance
            $table->index(['user_id', 'log_date']);
            $table->index('status');
            $table->index('user_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('logbook_entries');
    }
};
