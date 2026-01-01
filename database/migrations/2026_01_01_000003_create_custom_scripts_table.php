<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('custom_scripts', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->enum('position', ['head', 'body_start', 'body_end'])->default('head');
            $table->longText('code');
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('custom_scripts');
    }
};
