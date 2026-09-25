<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'must_set_password',
        'role',
        'stripe_customer_id',
        'stripe_payment_method_id',
        'card_brand',
        'card_last4',
        'card_exp_month',
        'card_exp_year',
        'referral_code',
        'referred_by',
        'referral_earnings',
        'referral_count',
        'points',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'must_set_password' => 'boolean',
            'referral_earnings' => 'decimal:2',
        ];
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function posts()
    {
        return $this->hasMany(Post::class);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    // Referral relationships
    public function referrer()
    {
        return $this->belongsTo(User::class, 'referred_by');
    }

    public function referrals()
    {
        return $this->hasMany(Referral::class, 'referrer_id');
    }

    public function referredBy()
    {
        return $this->hasOne(Referral::class, 'referred_id');
    }

    public function pointsTransactions()
    {
        return $this->hasMany(PointsTransaction::class);
    }

    // Referral methods
    public function generateReferralCode(): string
    {
        if ($this->referral_code) {
            return $this->referral_code;
        }

        do {
            $code = strtoupper(substr($this->name, 0, 3) . rand(1000, 9999));
        } while (User::where('referral_code', $code)->exists());

        $this->update(['referral_code' => $code]);
        return $code;
    }

    public function getReferralCode(): ?string
    {
        if (!$this->referral_code) {
            return $this->generateReferralCode();
        }
        return $this->referral_code;
    }

    public function getReferralUrl(): string
    {
        return route('register', ['ref' => $this->getReferralCode()]);
    }

    public function incrementReferralCount(): void
    {
        $this->increment('referral_count');
    }

    public function addReferralEarnings(float $amount): void
    {
        $this->increment('referral_earnings', $amount);
    }

    // Points methods
    public function addPoints(int $points, string $type, string $description = null, $relatedId = null, string $relatedType = null): PointsTransaction
    {
        $this->increment('points', $points);
        
        return PointsTransaction::create([
            'user_id' => $this->id,
            'points' => $points,
            'type' => $type,
            'description' => $description,
            'related_id' => $relatedId,
            'related_type' => $relatedType,
            'balance_after' => $this->fresh()->points,
        ]);
    }

    public function deductPoints(int $points, string $type, string $description = null, $relatedId = null, string $relatedType = null): PointsTransaction
    {
        $this->decrement('points', $points);
        
        return PointsTransaction::create([
            'user_id' => $this->id,
            'points' => -$points,
            'type' => $type,
            'description' => $description,
            'related_id' => $relatedId,
            'related_type' => $relatedType,
            'balance_after' => $this->fresh()->points,
        ]);
    }

    public function getPoints(): int
    {
        return $this->points ?? 0;
    }

    public function hasEnoughPoints(int $points): bool
    {
        return $this->getPoints() >= $points;
    }

    // Role helper methods
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isManager(): bool
    {
        return $this->role === 'manager';
    }

    public function isSeller(): bool
    {
        return $this->role === 'seller';
    }

    public function canSell(): bool
    {
        return in_array($this->role, ['seller', 'manager', 'admin']);
    }

    public function upgradeToSeller(): void
    {
        if ($this->role === 'user') {
            $this->update(['role' => 'seller']);
        }
    }
}
