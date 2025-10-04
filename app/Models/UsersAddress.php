<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UsersAddress extends Model
{
    protected $table = 'users_address';
    protected $fillable = [
        'user_id',
        'pincode',
        'address_line_1',
        'address_line_2',
        'type',
        'city',
        'state',
        'country',
        'status',
    ];
}
