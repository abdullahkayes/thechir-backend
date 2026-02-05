<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Brand extends Model
{
    protected $fillable = ['name', 'brand_image', 'description', 'is_active'];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // Accessor for logo (maps to brand_image)
    public function getLogoAttribute()
    {
        return $this->attributes['brand_image'] ?? null;
    }

    // Mutator for logo (maps to brand_image)
    public function setLogoAttribute($value)
    {
        $this->attributes['brand_image'] = $value;
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
