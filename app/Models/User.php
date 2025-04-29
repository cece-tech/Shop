<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'avatar',
        'stripe_connect_id',
        'stripe_connect_verified',
        'payouts_enabled',
        'total_sales',
        'total_earnings',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'stripe_connect_verified' => 'boolean',
        'payouts_enabled' => 'boolean',
        'total_sales' => 'integer',
        'total_earnings' => 'float',
    ];

    /**
     * Check if user is an admin.
     *
     * @return bool
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * Check if user is a seller.
     *
     * @return bool
     */
    public function isSeller(): bool
    {
        return $this->role === 'seller';
    }

    /**
     * Check if user is a buyer.
     *
     * @return bool
     */
    public function isBuyer(): bool
    {
        return $this->role === 'buyer';
    }

    /**
     * Get the store associated with the user.
     */
    public function store()
    {
        return $this->hasOne(Store::class);
    }

    /**
     * Get the orders placed by the user.
     */
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class, 'user_id');
    }

    /**
     * Get the orders received by the seller.
     */
    public function sellerOrders(): HasMany
    {
        return $this->hasMany(Order::class, 'seller_id');
    }

    /**
     * Get the transactions where user is the buyer.
     */
    public function buyerTransactions(): HasMany
    {
        return $this->hasMany(Transaction::class, 'buyer_id');
    }

    /**
     * Get the transactions where user is the seller.
     */
    public function sellerTransactions(): HasMany
    {
        return $this->hasMany(Transaction::class, 'seller_id');
    }

    /**
     * Get the products created by the user.
     */
    public function products(): HasMany
    {
        return $this->hasMany(Product::class, 'seller_id');
    }

    /**
     * Check if seller has Stripe Connect setup.
     *
     * @return bool
     */
    public function hasStripeConnected(): bool
    {
        return $this->isSeller() && 
               $this->stripe_connect_id && 
               $this->stripe_connect_verified;
    }

    /**
     * Update seller metrics after a successful sale.
     *
     * @param float $amount
     * @return void
     */
    public function updateSellerMetrics(float $amount): void
    {
        $this->increment('total_sales');
        $this->increment('total_earnings', $amount);
        $this->save();
    }
}