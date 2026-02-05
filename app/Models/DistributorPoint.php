<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DistributorPoint extends Model
{
    protected $fillable = [
        'name',
        'address',
        'city',
        'state',
        'zip_code',
        'country',
        'lat',
        'lng',
        'phone',
        'email',
        'status',
        'google_maps_api_key',
        'locationiq_api_key',
    ];

    protected $casts = [
        'lat' => 'decimal:8',
        'lng' => 'decimal:8',
    ];

    // Accessor for latitude (maps to lat)
    public function getLatitudeAttribute()
    {
        return $this->attributes['lat'] ?? null;
    }

    // Mutator for latitude (maps to lat)
    public function setLatitudeAttribute($value)
    {
        $this->attributes['lat'] = $value;
    }

    // Accessor for longitude (maps to lng)
    public function getLongitudeAttribute()
    {
        return $this->attributes['lng'] ?? null;
    }

    // Mutator for longitude (maps to lng)
    public function setLongitudeAttribute($value)
    {
        $this->attributes['lng'] = $value;
    }

    // Accessor for is_active (maps to status)
    public function getIsActiveAttribute()
    {
        return $this->attributes['status'] === 'active';
    }

    // Mutator for is_active (maps to status)
    public function setIsActiveAttribute($value)
    {
        $this->attributes['status'] = $value ? 'active' : 'inactive';
    }

    // Accessor for api_key_index (returns null for backward compatibility)
    public function getApiKeyIndexAttribute()
    {
        return null;
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Get the latitude attribute.
     * Maps lat database column to latitude accessor.
     */
    public function getLatAttribute($value)
    {
        return $value;
    }

    /**
     * Get the longitude attribute.
     * Maps lng database column to longitude accessor.
     */
    public function getLngAttribute($value)
    {
        return $value;
    }
}
