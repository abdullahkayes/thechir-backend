<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ZipCoordinate extends Model
{
    protected $fillable = ['zip', 'latitude', 'longitude'];

    protected $primaryKey = 'zip';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $casts = [
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:11',
    ];

    /**
     * Get the route key for the model.
     */
    public function getRouteKeyName(): string
    {
        return 'zip';
    }
}
