<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->string('image_alt')->nullable()->after('image');
            $table->string('sample_file')->nullable()->after('download_file');
            $table->string('sketchup_version')->nullable()->after('file_size');
            $table->unsignedInteger('download_count')->default(0)->after('file_count');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['image_alt', 'sample_file', 'sketchup_version', 'download_count']);
        });
    }
};
