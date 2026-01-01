<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('stripe_customer_id')->nullable()->after('role');
            $table->string('stripe_payment_method_id')->nullable()->after('stripe_customer_id');
            $table->string('card_brand')->nullable()->after('stripe_payment_method_id');
            $table->string('card_last4', 4)->nullable()->after('card_brand');
            $table->unsignedTinyInteger('card_exp_month')->nullable()->after('card_last4');
            $table->unsignedSmallInteger('card_exp_year')->nullable()->after('card_exp_month');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'stripe_customer_id',
                'stripe_payment_method_id',
                'card_brand',
                'card_last4',
                'card_exp_month',
                'card_exp_year',
            ]);
        });
    }
};
