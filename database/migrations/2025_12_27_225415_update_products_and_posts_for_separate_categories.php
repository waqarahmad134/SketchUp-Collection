<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Drop old foreign key constraints
        Schema::table('products', function (Blueprint $table) {
            $table->dropForeign(['category_id']);
        });

        Schema::table('posts', function (Blueprint $table) {
            $table->dropForeign(['category_id']);
        });

        // Re-add foreign keys pointing to new tables
        Schema::table('products', function (Blueprint $table) {
            $table->foreignId('category_id')->nullable()->change()
                ->constrained('product_categories')->onDelete('set null');
        });

        Schema::table('posts', function (Blueprint $table) {
            $table->foreignId('category_id')->nullable()->change()
                ->constrained('post_categories')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropForeign(['category_id']);
            $table->foreignId('category_id')->nullable()->change()
                ->constrained('categories')->onDelete('set null');
        });

        Schema::table('posts', function (Blueprint $table) {
            $table->dropForeign(['category_id']);
            $table->foreignId('category_id')->nullable()->change()
                ->constrained('categories')->onDelete('set null');
        });
    }
};
