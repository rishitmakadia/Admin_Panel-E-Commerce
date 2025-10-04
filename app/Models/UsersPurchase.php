<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UsersPurchase extends Model
{
    protected $table = 'users_purchase';
    protected $fillable = [
        'order_id',
        'user_id',
        'product_id',
        'product_type',
        'quantity',
        'price',
        'total_price',
        'status'
    ];
}
