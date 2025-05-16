<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    protected $fillable = [
        'code',
        'description',
        'type',
        'value',
        'min_order_amount',
        'max_uses',
        'used_count',
        'is_active',
        'starts_at',
        'expires_at',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'starts_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    /**
     * Check if the coupon is valid for use.
     *
     * @param float $orderAmount
     * @return bool
     */
    public function isValid($orderAmount = 0)
    {
        // Check if coupon is active
        if (!$this->is_active) {
            return false;
        }

        // Check if coupon has expired
        if ($this->expires_at && now()->gt($this->expires_at)) {
            return false;
        }

        // Check if coupon has started
        if ($this->starts_at && now()->lt($this->starts_at)) {
            return false;
        }

        // Check if coupon has reached max uses
        if ($this->max_uses && $this->used_count >= $this->max_uses) {
            return false;
        }

        // Check if order meets minimum amount
        if ($orderAmount < $this->min_order_amount) {
            return false;
        }

        return true;
    }

    /**
     * Calculate the discount amount.
     *
     * @param float $orderAmount
     * @return float
     */
    public function calculateDiscount($orderAmount)
    {
        if (!$this->isValid($orderAmount)) {
            return 0;
        }

        if ($this->type === 'fixed') {
            return min($this->value, $orderAmount);
        }

        if ($this->type === 'percentage') {
            return ($orderAmount * $this->value) / 100;
        }

        return 0;
    }

    /**
     * Increment the used count.
     *
     * @return void
     */
    public function incrementUsedCount()
    {
        $this->increment('used_count');
    }
}
