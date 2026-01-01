<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    public function run(): void
    {
        Setting::updateOrCreate(
            ['key' => 'stripe_checkout_url'],
            ['value' => env('STRIPE_CHECKOUT_URL', '')]
        );
    }
}

