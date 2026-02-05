<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Analytic extends Model
{
    use HasFactory;

    protected $fillable = [
        'session_id',
        'page_url',
        'ip_address',
        'user_agent',
        'visit_time',
        'time_on_page',
    ];
}
