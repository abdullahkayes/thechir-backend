<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Seo extends Model
{
    protected $fillable = [
        'model_type',
        'model_id',
        'description',
        'title',
        'image',
        'author',
        'robots',
        'canonical_url',
        'url',
        'published_time',
        'modified_time',
    ];

    public function model()
    {
        return $this->morphTo();
    }
}
