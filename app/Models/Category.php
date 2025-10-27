<?php

namespace App\Models;
use Illuminate\Database\Eloquent\SoftDeletes;

use Illuminate\Database\Eloquent\Model;

class Category extends Model

{
    use SoftDeletes;
    protected $fillable=[
     'category_name',
     'category_image',
    ];
    function rel_to_subcategory(){
        return $this->hasMany(Subcategory::class,'category_id');
    }
}
