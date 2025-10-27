<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderTracking extends Model
{
    use HasFactory;

    // 1. Define the table name (adjust if your table is named differently)
    protected $table = 'order_tracking';

    // 2. Specify fillable fields (the ones you can insert or update)
    protected $fillable = [
        'order_id',
        'status',
        'description',
        'location',
    ];

    // 3. Define the inverse relationship (A history record belongs to one Order)
    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id');
    }
}
