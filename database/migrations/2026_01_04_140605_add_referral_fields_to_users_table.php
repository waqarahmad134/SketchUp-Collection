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
        Schema::table('users', function (Blueprint $table) {
            $table->string('referral_code')->unique()->nullable()->after('email');
            $table->foreignId('referred_by')->nullable()->after('referral_code')->constrained('users')->onDelete('set null');
            $table->decimal('referral_earnings', 10, 2)->default(0)->after('referred_by');
            $table->integer('referral_count')->default(0)->after('referral_earnings');
            
            $table->index('referral_code');
            $table->index('referred_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['referred_by']);
            $table->dropIndex(['referral_code']);
            $table->dropIndex(['referred_by']);
            $table->dropColumn(['referral_code', 'referred_by', 'referral_earnings', 'referral_count']);
        });
    }
};
