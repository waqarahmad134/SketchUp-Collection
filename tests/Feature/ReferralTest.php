<?php

namespace Tests\Feature;

use App\Models\Referral;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReferralTest extends TestCase
{
    use RefreshDatabase;

    public function test_signup_with_valid_referral_code_creates_referral(): void
    {
        $referrer = User::factory()->create();
        $referrer->generateReferralCode();

        $response = $this->post(route('register.submit'), [
            'name' => 'Referred User',
            'email' => 'referred@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'referral_code' => $referrer->referral_code,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('referrals', [
            'referrer_id' => $referrer->id,
            'referred_id' => User::where('email', 'referred@example.com')->first()->id,
        ]);
    }

    public function test_signup_with_invalid_referral_code_fails(): void
    {
        $response = $this->post(route('register.submit'), [
            'name' => 'New User',
            'email' => 'new@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'referral_code' => 'NOPE123',
        ]);

        $response->assertSessionHasErrors('referral_code');
        $this->assertDatabaseMissing('users', ['email' => 'new@example.com']);
    }

    public function test_self_referral_is_blocked(): void
    {
        $user = User::factory()->create(['email' => 'self@example.com']);
        $user->generateReferralCode();

        $response = $this->post(route('register.submit'), [
            'name' => 'Self',
            'email' => 'self@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'referral_code' => $user->referral_code,
        ]);

        $response->assertSessionHasErrors('referral_code');
    }

    public function test_mark_as_rewarded_updates_status_and_amount(): void
    {
        $referrer = User::factory()->create();
        $referred = User::factory()->create();
        $referral = Referral::create([
            'referrer_id' => $referrer->id,
            'referred_id' => $referred->id,
            'status' => 'pending',
        ]);

        $referral->markAsRewarded(12.50, 'purchase');

        $this->assertEquals('rewarded', $referral->fresh()->status);
        $this->assertEquals(12.50, (float) $referral->fresh()->reward_amount);
        $this->assertNotNull($referral->fresh()->completed_at);
    }
}
