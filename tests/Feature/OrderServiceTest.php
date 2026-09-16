<?php

namespace Tests\Feature;

use App\Models\Coupon;
use App\Models\CouponUsage;
use App\Models\Order;
use App\Models\PaymentAttempt;
use App\Models\Product;
use App\Models\Referral;
use App\Models\User;
use App\Services\OrderService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderServiceTest extends TestCase
{
    use RefreshDatabase;

    protected function makeProduct(float $price = 100): Product
    {
        return Product::create([
            'title' => 'Test Bundle',
            'slug' => 'test-bundle-' . uniqid(),
            'price' => $price,
            'is_active' => true,
        ]);
    }

    protected function makeAttempt(User $user, Product $product, array $overrides = []): PaymentAttempt
    {
        return PaymentAttempt::create(array_merge([
            'gateway_slug' => 'stripe',
            'user_id' => $user->id,
            'cart' => [
                ['id' => $product->id, 'title' => $product->title, 'slug' => $product->slug, 'price' => $product->price, 'qty' => 1],
            ],
            'subtotal' => $product->price,
            'tax' => 0,
            'discount' => 0,
            'total' => $product->price,
            'currency' => 'USD',
            'status' => 'pending',
        ], $overrides));
    }

    protected function paymentPayload(): array
    {
        return [
            'gateway' => 'stripe',
            'transaction_id' => 'txn_' . uniqid(),
            'currency' => 'USD',
        ];
    }

    public function test_complete_from_attempt_creates_completed_order(): void
    {
        $user = User::factory()->create();
        $product = $this->makeProduct(100);
        $attempt = $this->makeAttempt($user, $product);

        $order = app(OrderService::class)->completeFromAttempt($attempt, $this->paymentPayload());

        $this->assertNotNull($order);
        $this->assertEquals('completed', $order->status);
        $this->assertEquals(100, (float) $order->total);
        $this->assertEquals(1, $order->items()->count());
        $this->assertEquals('completed', $attempt->fresh()->status);
    }

    public function test_coupon_discount_applied_and_usage_recorded(): void
    {
        $user = User::factory()->create();
        $product = $this->makeProduct(100);
        $coupon = Coupon::create([
            'code' => 'SAVE10', 'type' => 'percent', 'value' => 10,
            'min_order' => 0, 'used_count' => 0, 'usage_per_user' => 5, 'is_active' => true,
        ]);
        $attempt = $this->makeAttempt($user, $product, ['coupon_code' => 'SAVE10']);

        $order = app(OrderService::class)->completeFromAttempt($attempt, $this->paymentPayload());

        $this->assertEquals(10, (float) $order->discount);
        $this->assertEquals(90, (float) $order->total);
        $this->assertDatabaseHas('coupon_usages', [
            'coupon_id' => $coupon->id,
            'user_id' => $user->id,
            'order_id' => $order->id,
            'discount_amount' => 10,
        ]);
        $this->assertEquals(1, $coupon->fresh()->used_count);
    }

    public function test_invalid_coupon_on_attempt_is_ignored(): void
    {
        $user = User::factory()->create();
        $product = $this->makeProduct(100);
        Coupon::create([
            'code' => 'EXPIRED', 'type' => 'percent', 'value' => 50,
            'min_order' => 0, 'used_count' => 0, 'usage_per_user' => 5,
            'is_active' => true, 'expires_at' => now()->subDay(),
        ]);
        $attempt = $this->makeAttempt($user, $product, ['coupon_code' => 'EXPIRED']);

        $order = app(OrderService::class)->completeFromAttempt($attempt, $this->paymentPayload());

        $this->assertEquals(0, (float) $order->discount);
        $this->assertEquals(100, (float) $order->total);
        $this->assertEquals(0, CouponUsage::count());
    }

    public function test_coins_redeemed_as_discount_and_deducted(): void
    {
        $user = User::factory()->create(['points' => 5000]);
        $product = $this->makeProduct(100);
        // 2000 coins at 1000 per dollar = $2 discount
        $attempt = $this->makeAttempt($user, $product, ['coins_to_use' => 2000]);

        $order = app(OrderService::class)->completeFromAttempt($attempt, $this->paymentPayload());

        $this->assertEquals(2, (float) $order->discount);
        $this->assertEquals(98, (float) $order->total);
        $this->assertEquals(3000, $user->fresh()->points);
    }

    public function test_referral_reward_paid_on_first_purchase(): void
    {
        $referrer = User::factory()->create();
        $user = User::factory()->create(['referred_by' => $referrer->id]);
        Referral::create(['referrer_id' => $referrer->id, 'referred_id' => $user->id, 'status' => 'pending']);
        $product = $this->makeProduct(100);
        $attempt = $this->makeAttempt($user, $product);

        $order = app(OrderService::class)->completeFromAttempt($attempt, $this->paymentPayload());

        $referral = Referral::where('referred_id', $user->id)->first();
        $this->assertEquals('rewarded', $referral->status);
        // Default 10% of $100 = $10 reward
        $this->assertEquals(10, (float) $referral->reward_amount);
        $this->assertEquals(10, (float) $referrer->fresh()->referral_earnings);
        // Reward points: 10 * 1000 points per dollar = 10000
        $this->assertEquals(10000, $referrer->fresh()->points);
    }

    public function test_duplicate_completion_is_idempotent(): void
    {
        $user = User::factory()->create();
        $product = $this->makeProduct(100);
        $attempt = $this->makeAttempt($user, $product);
        $service = app(OrderService::class);

        $first = $service->completeFromAttempt($attempt, $this->paymentPayload());
        $second = $service->completeFromAttempt($attempt, $this->paymentPayload());

        $this->assertNotNull($first);
        $this->assertNull($second);
        $this->assertEquals(1, Order::count());
    }
}
