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
        Schema::create('coupons', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->enum('type', ['flat', 'percentage'])->default('percentage');
            $table->decimal('value', 10, 2); // Discount value (flat amount or percentage)
            $table->decimal('min_order', 10, 2)->default(0); // Minimum order amount
            $table->decimal('max_discount', 10, 2)->nullable(); // Maximum discount (for percentage type)
            $table->integer('usage_limit')->nullable(); // Total usage limit (null = unlimited)
            $table->integer('used_count')->default(0); // How many times it's been used
            $table->integer('usage_per_user')->default(1); // How many times one user can use it
            $table->dateTime('expires_at')->nullable(); // Expiry date
            $table->boolean('is_active')->default(true);
            $table->text('description')->nullable();
            $table->timestamps();
            
            $table->index('code');
            $table->index('is_active');
            $table->index('expires_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('coupons');
    }
};
