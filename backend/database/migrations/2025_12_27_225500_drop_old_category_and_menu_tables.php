<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Drop old tables that are no longer needed
        Schema::dropIfExists('menu_items');
        Schema::dropIfExists('categories');
    }

    public function down(): void
    {
        // Restore old tables if needed (simplified version)
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('type')->default('product');
            $table->timestamps();
        });
    }
};

