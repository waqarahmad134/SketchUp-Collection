<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payment_attempts', function (Blueprint $table) {
            $table->id();
            $table->string('token', 64)->unique();
            $table->string('gateway_slug');
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->json('cart');
            $table->decimal('subtotal', 12, 2)->default(0);
            $table->decimal('tax', 12, 2)->default(0);
            $table->decimal('discount', 12, 2)->default(0);
            $table->decimal('total', 12, 2)->default(0);
            $table->string('currency', 8)->default('USD');
            $table->string('coupon_code')->nullable();
            $table->unsignedBigInteger('coins_to_use')->default(0);
            $table->string('status', 20)->default('pending'); // pending, completed, failed
            $table->string('gateway_reference')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

            $table->index(['gateway_slug', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_attempts');
    }
};
