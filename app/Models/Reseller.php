<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Reseller extends Authenticatable
{
    use HasApiTokens, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'venmo_zelle_id',
        'unique_ref_id',
        'ref_link',
        'discount_code',
        'commission_percentage',
        'status',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'commission_percentage' => 'decimal:2',
    ];

    public function commissions()
    {
        return $this->hasMany(Commission::class, 'reseller_id');
    }

    public function orders()
    {
        return $this->hasMany(Order::class, 'reseller_id');
    }
}
