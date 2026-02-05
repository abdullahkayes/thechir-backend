<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BannerDown extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = ['sub_title', 'title', 'image', 'banner_id'];
}
