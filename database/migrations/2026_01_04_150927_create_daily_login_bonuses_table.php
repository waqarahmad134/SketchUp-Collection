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
        Schema::create('daily_login_bonuses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->date('claimed_date');
            $table->integer('streak_day')->default(1); // Day 1-7 of the week
            $table->date('week_start_date'); // Monday of the week
            $table->integer('points_awarded');
            $table->timestamps();
            
            $table->unique(['user_id', 'claimed_date']);
            $table->index(['user_id', 'week_start_date']);
            $table->index('claimed_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('daily_login_bonuses');
    }
};
