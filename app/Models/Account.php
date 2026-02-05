<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Account extends Model
{
    protected $fillable = [
        'account_type_id',
        'name',
        'code',
        'description',
        'balance',
        'is_active',
    ];

    protected $casts = [
        'balance' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function accountType()
    {
        return $this->belongsTo(AccountType::class);
    }

    public function entryLines()
    {
        return $this->hasMany(AccountingEntryLine::class);
    }

    public function debit($amount)
    {
        $this->balance += $amount;
        $this->save();
    }

    public function credit($amount)
    {
        $this->balance -= $amount;
        $this->save();
    }
}
