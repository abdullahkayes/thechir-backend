<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'price' => 'decimal:2',
        'sell_price' => 'decimal:2',
        'cost_price' => 'decimal:2',
    ];

    function rel_to_product(){
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function customer()
    {
        return $this->belongsTo(Coustomer::class, 'coustomer_id');
    }

    public function reseller()
    {
        return $this->belongsTo(Reseller::class, 'reseller_id');
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

    public function color()
    {
        return $this->belongsTo(Color::class, 'color_id');
    }

    public function size()
    {
        return $this->belongsTo(Size::class, 'size_id');
    }
}
