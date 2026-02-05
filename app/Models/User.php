<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasRoles;


    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'photo',
        'phone',
        'venmo_zelle_id',
        'unique_ref_id',
        'ref_link',
        'discount_code',
        'business_name',
        'ein',
        'resale_certificate_path',
        'shipping_address',
        'status',
        'ref_id',
        'commission_percentage',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'commission_percentage' => 'decimal:2',
        ];
    }

    public function commissions()
    {
        return $this->hasMany(Commission::class, 'reseller_id');
    }

    public function referredUsers()
    {
        return $this->hasMany(User::class, 'ref_id', 'unique_ref_id');
    }

    public function referrer()
    {
        return $this->belongsTo(User::class, 'ref_id', 'unique_ref_id');
    }

    public function orders()
    {
        return $this->hasMany(Order::class, 'reseller_id');
    }
}
