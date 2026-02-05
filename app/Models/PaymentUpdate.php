<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentUpdate extends Model
{
    protected $fillable = ['order_id', 'amazon_id', 'status'];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function amazon()
    {
        return $this->belongsTo(Amazon::class);
    }
}
