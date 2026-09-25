<?php

namespace Tests\Feature;

use App\Models\Coupon;
use App\Models\CouponUsage;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CouponTest extends TestCase
{
    use RefreshDatabase;

    protected function makeCoupon(array $overrides = []): Coupon
    {
        return Coupon::create(array_merge([
            'code' => 'TEST10',
            'type' => 'percent',
            'value' => 10,
            'min_order' => 0,
            'max_discount' => null,
            'usage_limit' => null,
            'used_count' => 0,
            'usage_per_user' => 1,
            'expires_at' => null,
            'is_active' => true,
        ], $overrides));
    }

    public function test_valid_coupon_passes(): void
    {
        $coupon = $this->makeCoupon();
        $user = User::factory()->create();

        $result = $coupon->isValid($user->id, 100);

        $this->assertTrue($result['valid']);
    }

    public function test_inactive_coupon_fails(): void
    {
        $coupon = $this->makeCoupon(['is_active' => false]);

        $result = $coupon->isValid(null, 100);

        $this->assertFalse($result['valid']);
    }

    public function test_expired_coupon_fails(): void
    {
        $coupon = $this->makeCoupon(['expires_at' => now()->subDay()]);

        $result = $coupon->isValid(null, 100);

        $this->assertFalse($result['valid']);
    }

    public function test_min_order_not_met_fails(): void
    {
        $coupon = $this->makeCoupon(['min_order' => 50]);

        $result = $coupon->isValid(null, 20);

        $this->assertFalse($result['valid']);
    }

    public function test_usage_limit_reached_fails(): void
    {
        $coupon = $this->makeCoupon(['usage_limit' => 5, 'used_count' => 5]);

        $result = $coupon->isValid(null, 100);

        $this->assertFalse($result['valid']);
    }

    public function test_per_user_limit_reached_fails(): void
    {
        $coupon = $this->makeCoupon(['usage_per_user' => 1]);
        $user = User::factory()->create();
        CouponUsage::create([
            'coupon_id' => $coupon->id,
            'user_id' => $user->id,
            'discount_amount' => 10,
        ]);

        $result = $coupon->isValid($user->id, 100);

        $this->assertFalse($result['valid']);
    }

    public function test_flat_discount_capped_at_order_total(): void
    {
        $coupon = $this->makeCoupon(['type' => 'flat', 'value' => 200]);

        $this->assertEquals(50, $coupon->calculateDiscount(50));
        $this->assertEquals(200, $coupon->calculateDiscount(500));
    }

    public function test_percent_discount_respects_max_discount(): void
    {
        $coupon = $this->makeCoupon(['type' => 'percent', 'value' => 20, 'max_discount' => 15]);

        $this->assertEquals(15, $coupon->calculateDiscount(200)); // 20% = 40, capped at 15
        $this->assertEquals(10, $coupon->calculateDiscount(50));  // 20% = 10, under cap
    }
}
