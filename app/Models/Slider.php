<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Slider extends Model
{
    protected $fillable = ['slider_image', 'brand_id'];

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }
}
