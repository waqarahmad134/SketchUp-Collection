<?php

namespace Database\Seeders;

use App\Models\PaymentGateway;
use Illuminate\Database\Seeder;

class PaymentGatewaySeeder extends Seeder
{
    public function run(): void
    {
        $gateways = [
            ['name' => 'JazzCash', 'slug' => 'jazzcash', 'sort_order' => 1],
            ['name' => 'Easypaisa', 'slug' => 'easypaisa', 'sort_order' => 2],
            ['name' => 'PayPro', 'slug' => 'paypro', 'sort_order' => 3],
            ['name' => 'Paddle', 'slug' => 'paddle', 'sort_order' => 4],
            ['name' => 'Lemon Squeezy', 'slug' => 'lemon-squeezy', 'sort_order' => 5],
            ['name' => 'Polar', 'slug' => 'polar', 'sort_order' => 6],
        ];

        foreach ($gateways as $gateway) {
            $definition = config("payment-gateways.definitions.{$gateway['slug']}", []);

            PaymentGateway::updateOrCreate(
                ['slug' => $gateway['slug']],
                [
                    'name' => $gateway['name'],
                    'description' => $definition['description'] ?? null,
                    'is_enabled' => false,
                    'test_mode' => true,
                    'sort_order' => $gateway['sort_order'],
                ]
            );
        }
    }
}
