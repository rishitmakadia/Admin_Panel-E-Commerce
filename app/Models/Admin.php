<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Admin extends Authenticatable
{
    use HasFactory, Notifiable, HasApiTokens;

    protected $table = 'admin';
    protected $fillable = [
        'name',
        'email',
        'phone',
        'password',
        'api_token', //not required for admin
        'status'
    ];
    protected $hidden = [
        'password',
    ];
}
