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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description');
            $table->text('full_description')->nullable();
            $table->string('image');
            $table->json('images')->nullable(); // Additional images
            $table->decimal('price', 10, 2);
            $table->decimal('original_price', 10, 2);
            $table->boolean('is_bundle')->default(false);
            $table->string('category')->default('interior'); // interior, exterior, landscape, furniture, textures
            $table->json('features')->nullable(); // Array of features
            $table->string('file_size')->nullable();
            $table->integer('file_count')->default(0);
            $table->json('tags')->nullable(); // Array of tags
            $table->json('included_products')->nullable(); // Product IDs if bundle
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
