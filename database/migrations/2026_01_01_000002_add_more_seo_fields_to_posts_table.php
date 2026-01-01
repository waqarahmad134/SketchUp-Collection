<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            // Canonical URL
            $table->string('canonical_url')->nullable()->after('meta_description');
            
            // Robots
            $table->string('robots_index')->default('index')->after('canonical_url');
            $table->string('robots_follow')->default('follow')->after('robots_index');
            
            // Open Graph
            $table->string('og_title')->nullable()->after('robots_follow');
            $table->text('og_description')->nullable()->after('og_title');
            $table->string('og_image')->nullable()->after('og_description');
            $table->string('og_type')->default('article')->after('og_image');
            
            // Twitter Card
            $table->string('twitter_card')->default('summary_large_image')->after('og_type');
            $table->string('twitter_title')->nullable()->after('twitter_card');
            $table->text('twitter_description')->nullable()->after('twitter_title');
            $table->string('twitter_image')->nullable()->after('twitter_description');
            
            // Schema.org JSON-LD
            $table->json('schema_markup')->nullable()->after('twitter_image');
        });
    }

    public function down(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->dropColumn([
                'canonical_url',
                'robots_index',
                'robots_follow',
                'og_title',
                'og_description',
                'og_image',
                'og_type',
                'twitter_card',
                'twitter_title',
                'twitter_description',
                'twitter_image',
                'schema_markup',
            ]);
        });
    }
};
