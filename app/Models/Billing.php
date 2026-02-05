<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Billing extends Model
{
    protected $fillable = [
        'order_id',
        'coustomer_id',
        'reseller_id',
        'b2b_id',
        'distributer_id',
        'name',
        'company',
        'street',
        'apartment',
        'city',
        'phone',
        'email',
    ];

    public function customer()
    {
        return $this->belongsTo(Coustomer::class, 'coustomer_id');
    }

    public function reseller()
    {
        return $this->belongsTo(User::class, 'reseller_id');
    }

    public function b2b()
    {
        return $this->belongsTo(B2b::class, 'b2b_id');
    }

    public function amazon()
    {
        return $this->belongsTo(Amazon::class, 'amazon_id');
    }

    public function distributer()
    {
        return $this->belongsTo(Distributer::class, 'distributer_id');
    }
}
