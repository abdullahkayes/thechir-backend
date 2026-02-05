<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductInventory extends Model
{
    protected $fillable = [
        'product_id',
        'size_id',
        'color_id',
        'buy_price',
        'price',
        'discount_price',
        'reseller_price',
        'wholesale_price',
        'distributer_price',
        'amazon_price',
        'quantity',
        'weight_grams',
        'weight_unit',
        'expiry_date',
        'manufacture_date',
        'batch_number',
    ];

    protected $casts = [
        'expiry_date' => 'date',
        'manufacture_date' => 'date',
        'buy_price' => 'decimal:2',
        'price' => 'decimal:2',
        'discount_price' => 'decimal:2',
        'reseller_price' => 'decimal:2',
        'wholesale_price' => 'decimal:2',
        'distributer_price' => 'decimal:2',
        'amazon_price' => 'decimal:2',
        'quantity' => 'integer',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function size()
    {
        return $this->belongsTo(Size::class);
    }

    public function color()
    {
        return $this->belongsTo(Color::class);
    }

    public function getRouteKeyName()
    {
        return 'id';
    }

    /**
     * Check if the product is expired
     */
    public function isExpired()
    {
        return $this->expiry_date && $this->expiry_date->isPast();
    }

    /**
     * Check if the product is expiring soon (within 30 days)
     */
    public function isExpiringSoon($days = 30)
    {
        return $this->expiry_date &&
               $this->expiry_date->isFuture() &&
               $this->expiry_date->diffInDays(now()) <= $days;
    }

    /**
     * Get expiry status
     */
    public function getExpiryStatus()
    {
        if ($this->isExpired()) {
            return 'expired';
        } elseif ($this->isExpiringSoon()) {
            return 'expiring_soon';
        } elseif ($this->expiry_date) {
            return 'valid';
        }
        return 'no_expiry';
    }

    /**
     * Get days until expiry
     */
    public function getDaysUntilExpiry()
    {
        if (!$this->expiry_date) {
            return null;
        }

        return now()->diffInDays($this->expiry_date, false); // false = allow negative
    }
}
