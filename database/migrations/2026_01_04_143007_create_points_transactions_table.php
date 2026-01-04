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
        Schema::create('points_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->integer('points'); // Can be positive (earned) or negative (spent)
            $table->string('type'); // 'signup', 'referral', 'purchase', 'reward', 'spent', etc.
            $table->string('description')->nullable();
            $table->foreignId('related_id')->nullable(); // Related order, referral, etc.
            $table->string('related_type')->nullable(); // 'order', 'referral', etc.
            $table->integer('balance_after')->nullable(); // Balance after this transaction
            $table->timestamps();
            
            $table->index('user_id');
            $table->index('type');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('points_transactions');
    }
};
