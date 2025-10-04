<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UsersCart extends Model
{
    protected $table = 'users_cart';
    protected $fillable = [
        'user_id',
        'item_id',
        'item_type',
        'quantity',
        'price',
        'status',
    ];
}
