<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Supplier extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'address',
        'contact_person',
        'payment_terms',
        'notes',
        'company_name',
        'city',
        'country',
        'tax_id',
        'status',
        'total_payable',
        'total_paid',
    ];

    protected $casts = [
        'total_payable' => 'decimal:2',
        'total_paid' => 'decimal:2',
    ];

    public function purchaseOrders()
    {
        return $this->hasMany(PurchaseOrder::class);
    }

    public function getBalanceAttribute()
    {
        return $this->total_payable - $this->total_paid;
    }
}
