<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $table = 'orders'; // Fixed: Point to orders table for Cash on Delivery

    protected $guarded = ['id'];
    
    public $timestamps = true; // Ensure timestamps are automatically handled

    protected $casts = [
        'total' => 'decimal:2',
        'sub_total' => 'decimal:2',
        'tax' => 'decimal:2',
        'shipping_cost' => 'decimal:2',
        'sell_price' => 'decimal:2',
        'cost_price' => 'decimal:2',
        'delivery_charge' => 'decimal:2',
    ];

    // Make sure status is accessible
    protected $fillable = [
        'order_id',
        'coustomer_id',
        'reseller_id',
        'b2b_id',
        'distributer_id',
        'amazon_id',
        'total',
        'sub_total',
        'subtotal',
        'tax',
        'shipping_cost',
        'coupon',
        'discount',
        'payment_method',
        'balance_used',
        'status',
        'payment_status',
        'quantity',
        'sell_price',
        'cost_price',
        'delivery_charge',
        'created_at'
    ];

    public function orderProducts()
    {
        return $this->hasMany(OrderProduct::class, 'order_id', 'order_id');
    }

    public function products()
    {
        return $this->hasManyThrough(
            Product::class,
            OrderProduct::class,
            'order_id', // Foreign key on order_products table
            'id',       // Foreign key on products table
            'order_id', // Local key on orders table
            'product_id' // Foreign key on order_products table
        );
    }

    public function customer()
    {
        return $this->belongsTo(Coustomer::class, 'coustomer_id');
    }  

    public function inventoryMovements()
    {
        return $this->morphMany(InventoryMovement::class, 'reference');
    }

    public function accountingEntries()
    {
        return $this->morphMany(AccountingEntry::class, 'reference');
    }

    public function orderTracking()
    {
        return $this->hasOne(OrderTracking::class, 'order_id', 'id');
    }

    public function reseller()
    {
        return $this->belongsTo(User::class, 'reseller_id');
    }

    public function commissions()
    {
        return $this->hasMany(Commission::class, 'order_id');
    }

    public function b2b()
    {
        return $this->belongsTo(B2b::class, 'b2b_id');
    }

    public function amazon()
    {
        return $this->belongsTo(Amazon::class, 'amazon_id');
    }

    public function distributer()
    {
        return $this->belongsTo(Distributer::class, 'distributer_id');
    }

    public function paymentUpdates()
    {
        return $this->hasMany(PaymentUpdate::class, 'order_id');
    }

    // Remove the custom accessor that was causing the issue
    // The status will now be accessed directly from the orders table
    
    public function isCompleted()
    {
        // Status column not yet added to database, return false for now
        return false; // $this->status == 3; // Assuming 3 is completed
    }

    public function isDelivered()
    {
        // Status column not yet added to database, return false for now
        return false; // $this->status == 3; // Assuming 3 is delivered
    }

    public function getTotalAmountAttribute()
    {
        return $this->total;
    }
}
