<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            if (!Schema::hasColumn('products', 'is_digital')) {
                $table->boolean('is_digital')->default(true)->after('is_bundle');
            }
            if (!Schema::hasColumn('products', 'download_links')) {
                $table->json('download_links')->nullable()->after('features');
            }
            if (!Schema::hasColumn('products', 'download_file')) {
                $table->string('download_file')->nullable()->after('download_links');
            }
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            if (Schema::hasColumn('products', 'download_file')) {
                $table->dropColumn('download_file');
            }
            if (Schema::hasColumn('products', 'download_links')) {
                $table->dropColumn('download_links');
            }
            if (Schema::hasColumn('products', 'is_digital')) {
                $table->dropColumn('is_digital');
            }
        });
    }
};

