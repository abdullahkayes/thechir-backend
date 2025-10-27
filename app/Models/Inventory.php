<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Inventory extends Model
{
    function rel_to_col(){
        return $this->belongsTo(Color::class,'color_id');
    }
    function rel_to_size(){
        return $this->belongsTo(Size::class,'size_id');
    }
}
