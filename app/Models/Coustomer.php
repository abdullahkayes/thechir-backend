<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
class Coustomer extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $guarded =['id'];
    protected $guard ='coustomer';


}
