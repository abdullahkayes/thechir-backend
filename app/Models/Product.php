<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class Product extends Model
{
    use SoftDeletes;

    function rel_to_cat(){
        return $this->belongsTo(Category::class,'category_id');
    }
    function rel_to_sub(){
        return $this->belongsTo(Subcategory::class,'subcategory_id');
    }
    function rel_to_gal(){
        return $this->hasMany(Gallary::class,'product_id','id');
    }
    public function rel_to_inventory()
    {
        return $this->hasMany(Inventory::class);
    }
}
// rel_to_inventory