<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AccountingEntryLine extends Model
{
    protected $fillable = [
        'accounting_entry_id',
        'account_id',
        'type',
        'amount',
        'description',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
    ];

    public function accountingEntry()
    {
        return $this->belongsTo(AccountingEntry::class);
    }

    public function account()
    {
        return $this->belongsTo(Account::class);
    }
}
