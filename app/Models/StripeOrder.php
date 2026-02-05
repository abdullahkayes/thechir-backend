<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StripeOrder extends Model
{
    protected $fillable = [
        'order_id',
        'coustomer_id',
        'reseller_id',
        'b2b_id',
        'distributer_id',
        'amazon_id',
        'sub_total',
        'total',
        'discount',
        'payment_method',
        'coupon',
        'status',
        'name',
        'company',
        'street',
        'apartment',
        'city',
        'phone',
        'email',
    ];

    public function orderProducts()
    {
        return $this->hasMany(OrderProduct::class, 'order_id', 'order_id');
    }
}
