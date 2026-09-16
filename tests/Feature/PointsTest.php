<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PointsTest extends TestCase
{
    use RefreshDatabase;

    public function test_add_points_increases_balance_and_logs_transaction(): void
    {
        $user = User::factory()->create(['points' => 0]);

        $tx = $user->addPoints(500, 'signup', 'Welcome bonus');

        $this->assertEquals(500, $user->fresh()->points);
        $this->assertEquals(500, $tx->points);
        $this->assertEquals(500, $tx->balance_after);
        $this->assertEquals('signup', $tx->type);
    }

    public function test_deduct_points_decreases_balance_with_negative_entry(): void
    {
        $user = User::factory()->create(['points' => 1000]);

        $tx = $user->deductPoints(300, 'redeem', 'Coin discount');

        $this->assertEquals(700, $user->fresh()->points);
        $this->assertEquals(-300, $tx->points);
        $this->assertEquals(700, $tx->balance_after);
    }

    public function test_balance_after_reflects_running_total(): void
    {
        $user = User::factory()->create(['points' => 0]);

        $user->addPoints(500, 'signup', 'Welcome bonus');
        $tx = $user->addPoints(200, 'purchase', 'Order reward');

        $this->assertEquals(700, $tx->balance_after);
        $this->assertEquals(700, $user->fresh()->points);
    }

    public function test_earned_and_spent_scopes(): void
    {
        $user = User::factory()->create(['points' => 0]);
        $user->addPoints(500, 'signup', 'Welcome bonus');
        $user->deductPoints(200, 'redeem', 'Coin discount');

        $this->assertEquals(500, $user->pointsTransactions()->earned()->sum('points'));
        $this->assertEquals(-200, $user->pointsTransactions()->spent()->sum('points'));
    }
}
