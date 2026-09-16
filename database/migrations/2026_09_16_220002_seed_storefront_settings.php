<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Seed default storefront settings (WhatsApp button, announcement bar,
     * newsletter). Uses insert-if-missing so reruns never overwrite
     * values Waqar already configured.
     */
    public function up(): void
    {
        $defaults = [
            'whatsapp_number' => '',
            'whatsapp_message' => 'Hi! I have a question about SketchUp Collection.',
            'announcement_enabled' => '0',
            'announcement_text' => 'Mega Sale: up to 50% off all bundles this week!',
            'announcement_link' => '/bundles',
            'newsletter_heading' => 'Get 10% off your first order',
            'newsletter_subheading' => 'Join the newsletter for new bundles, free assets and exclusive discounts.',
        ];

        foreach ($defaults as $key => $value) {
            if (! DB::table('settings')->where('key', $key)->exists()) {
                DB::table('settings')->insert([
                    'key' => $key,
                    'value' => $value,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }

    public function down(): void
    {
        DB::table('settings')->whereIn('key', [
            'whatsapp_number',
            'whatsapp_message',
            'announcement_enabled',
            'announcement_text',
            'announcement_link',
            'newsletter_heading',
            'newsletter_subheading',
        ])->delete();
    }
};
