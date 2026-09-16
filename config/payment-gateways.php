<?php

use App\Services\Payments\EasypaisaDriver;
use App\Services\Payments\JazzCashDriver;
use App\Services\Payments\LemonSqueezyDriver;
use App\Services\Payments\PaddleDriver;
use App\Services\Payments\PayProDriver;
use App\Services\Payments\PolarDriver;

return [

    /*
    |--------------------------------------------------------------------------
    | Payment Gateway Definitions
    |--------------------------------------------------------------------------
    |
    | Each gateway: driver class, charge currency, credential fields shown in
    | the admin panel, and setup help. Secrets are stored encrypted.
    | "test_mode" on the gateway record switches sandbox/live endpoints.
    |
    */

    // USD -> PKR rate used when a PKR gateway charges a USD cart total.
    // Editable from Admin > Payment Gateways.
    'usd_to_pkr_rate' => env('USD_TO_PKR_RATE', 278),

    'definitions' => [

        'jazzcash' => [
            'name' => 'JazzCash',
            'currency' => 'PKR',
            'driver' => JazzCashDriver::class,
            'description' => 'JazzCash mobile wallet and card payments (Pakistan).',
            'uses_form_post' => true,
            'fields' => [
                ['key' => 'merchant_id', 'label' => 'Merchant ID', 'type' => 'text', 'required' => true],
                ['key' => 'password', 'label' => 'Password', 'type' => 'password', 'required' => true],
                ['key' => 'integrity_salt', 'label' => 'Integrity Salt', 'type' => 'password', 'required' => true],
            ],
            'setup_help' => 'Get Merchant ID, Password and Integrity Salt from the JazzCash merchant portal (merchant.jazzcash.com.pk). Enable TEST MODE while using sandbox credentials. The Return URL below must be registered/whitelisted in your JazzCash merchant settings.',
        ],

        'easypaisa' => [
            'name' => 'Easypaisa',
            'currency' => 'PKR',
            'driver' => EasypaisaDriver::class,
            'description' => 'Easypaisa mobile wallet payments (Pakistan).',
            'fields' => [
                ['key' => 'store_id', 'label' => 'Store ID', 'type' => 'text', 'required' => true],
                ['key' => 'username', 'label' => 'API Username', 'type' => 'text', 'required' => true],
                ['key' => 'password', 'label' => 'API Password', 'type' => 'password', 'required' => true],
                ['key' => 'hash_key', 'label' => 'Hash Key', 'type' => 'password', 'required' => true],
            ],
            'setup_help' => 'Get Store ID and API credentials from your Easypaisa merchant account. Enable TEST MODE while using sandbox credentials. Register the Return URL below in your Easypaisa merchant settings.',
        ],

        'paypro' => [
            'name' => 'PayPro',
            'currency' => 'PKR',
            'driver' => PayProDriver::class,
            'description' => 'PayPro hosted checkout: wallets, cards and bank transfer (Pakistan).',
            'fields' => [
                ['key' => 'merchant_id', 'label' => 'Merchant ID', 'type' => 'text', 'required' => true],
                ['key' => 'merchant_password', 'label' => 'Merchant Password', 'type' => 'password', 'required' => true],
            ],
            'setup_help' => 'Get your Merchant ID and password from the PayPro merchant dashboard (paypro.com.pk). Enable TEST MODE while using sandbox credentials. Set the Return URL below as your callback/redirect URL in PayPro.',
        ],

        'paddle' => [
            'name' => 'Paddle',
            'currency' => 'USD',
            'driver' => PaddleDriver::class,
            'description' => 'Paddle Billing: international cards and PayPal, Paddle handles tax.',
            'fields' => [
                ['key' => 'api_key', 'label' => 'API Key', 'type' => 'password', 'required' => true],
                ['key' => 'webhook_secret', 'label' => 'Webhook Secret', 'type' => 'password', 'required' => false, 'help' => 'From Paddle > Developer tools > Notifications. Used to verify webhooks.'],
            ],
            'setup_help' => 'Create an API key at Paddle > Developer tools > Authentication. TEST MODE uses the Paddle sandbox (sandbox-api.paddle.com); use a sandbox API key with test mode on. In Paddle > Checkout settings, set your checkout success URL to the Return URL below (Paddle appends the transaction id automatically). Register the Webhook URL below at Paddle > Developer tools > Notifications (events: transaction.completed).',
        ],

        'lemon-squeezy' => [
            'name' => 'Lemon Squeezy',
            'currency' => 'USD',
            'driver' => LemonSqueezyDriver::class,
            'description' => 'Lemon Squeezy hosted checkout for digital products.',
            'fields' => [
                ['key' => 'api_key', 'label' => 'API Key', 'type' => 'password', 'required' => true],
                ['key' => 'store_id', 'label' => 'Store ID', 'type' => 'text', 'required' => true],
                ['key' => 'variant_id', 'label' => 'Variant ID', 'type' => 'text', 'required' => true, 'help' => 'Create a generic "Store Order" product in Lemon Squeezy and paste its variant ID. The real order total is stored in checkout metadata.'],
                ['key' => 'signing_secret', 'label' => 'Signing Secret', 'type' => 'password', 'required' => false, 'help' => 'From Settings > Webhooks. Used to verify webhook signatures.'],
            ],
            'setup_help' => 'Create an API key at Lemon Squeezy > Settings > API. TEST MODE uses test mode checkouts. Add the Webhook URL below at Settings > Webhooks (event: order_created) and paste the signing secret above.',
        ],

        'polar' => [
            'name' => 'Polar',
            'currency' => 'USD',
            'driver' => PolarDriver::class,
            'description' => 'Polar.sh hosted checkout for digital products.',
            'fields' => [
                ['key' => 'access_token', 'label' => 'Access Token', 'type' => 'password', 'required' => true],
                ['key' => 'product_id', 'label' => 'Product ID', 'type' => 'text', 'required' => true, 'help' => 'Create a generic "Store Order" product in Polar and paste its product ID. The real order total is stored in checkout metadata.'],
                ['key' => 'webhook_secret', 'label' => 'Webhook Secret', 'type' => 'password', 'required' => false, 'help' => 'From Polar > Webhooks. Used to verify webhook signatures.'],
            ],
            'setup_help' => 'Create an access token at Polar > Settings. TEST MODE uses the Polar sandbox (sandbox-api.polar.sh). Register the Webhook URL below at Polar > Webhooks (event: checkout.updated) and paste the secret above.',
        ],

    ],
];
