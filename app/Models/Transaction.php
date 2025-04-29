<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Transaction extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'order_id',
        'buyer_id',
        'seller_id',
        'product_id',
        'amount',
        'platform_fee',
        'seller_amount',
        'currency',
        'stripe_payment_intent_id',
        'stripe_transfer_id',
        'status',
        'payment_method',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'amount' => 'float',
        'platform_fee' => 'float',
        'seller_amount' => 'float',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the order associated with the transaction.
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Get the buyer associated with the transaction.
     */
    public function buyer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'buyer_id');
    }

    /**
     * Get the seller associated with the transaction.
     */
    public function seller(): BelongsTo
    {
        return $this->belongsTo(User::class, 'seller_id');
    }

    /**
     * Get the product associated with the transaction.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Calculate the seller amount.
     *
     * @param float $amount
     * @param float $platformFeePercentage
     * @return float
     */
    public static function calculateSellerAmount(float $amount, float $platformFeePercentage = 0.10): float
    {
        $platformFee = $amount * $platformFeePercentage;
        return $amount - $platformFee;
    }

    /**
     * Calculate the platform fee.
     *
     * @param float $amount
     * @param float $platformFeePercentage
     * @return float
     */
    public static function calculatePlatformFee(float $amount, float $platformFeePercentage = 0.10): float
    {
        return $amount * $platformFeePercentage;
    }

    /**
     * Scope a query to only include completed transactions.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    /**
     * Scope a query to only include transactions for a specific seller.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int $sellerId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeForSeller($query, $sellerId)
    {
        return $query->where('seller_id', $sellerId);
    }

    /**
     * Get formatted amount.
     *
     * @return string
     */
    public function getFormattedAmountAttribute(): string
    {
        return number_format($this->amount, 2);
    }

    /**
     * Get formatted seller amount.
     *
     * @return string
     */
    public function getFormattedSellerAmountAttribute(): string
    {
        return number_format($this->seller_amount, 2);
    }

    /**
     * Get formatted platform fee.
     *
     * @return string
     */
    public function getFormattedPlatformFeeAttribute(): string
    {
        return number_format($this->platform_fee, 2);
    }
}