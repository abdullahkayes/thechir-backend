<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderProduct extends Model
{
    protected $fillable = [
        'order_id',
        'product_id',
        'quantity',
        'price',
        'cogs',
        'sell_price',
        'cost_price',
        'color_id',
        'size_id',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'price' => 'decimal:2',
        'cogs' => 'decimal:2',
        'sell_price' => 'decimal:2',
        'cost_price' => 'decimal:2',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id', 'order_id');
    }

    public function rel_to_product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function getProfitAttribute()
    {
        return ($this->price - $this->cogs) * $this->quantity;
    }

    public function getMarginAttribute()
    {
        return $this->cogs > 0 ? (($this->price - $this->cogs) / $this->price) * 100 : 0;
    }
}
