<?php

namespace App\Models;
use Illuminate\Database\Eloquent\SoftDeletes;

use Illuminate\Database\Eloquent\Model;

class SliderVideo extends Model
{
    
    use SoftDeletes;

    protected $fillable = [
        'video',
        'thumbnail',
        'product_image',
        'name',
        'price',
        'discount_price',
        'wholesale_price',
        'reseller_price',
        'distributer_price',
        'amazon_price',
        'product_id',
    ];
}
