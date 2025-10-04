<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderList extends Model
{
    protected $table = 'order_list';
    protected $fillable = [
        'receipt',
        'payment_id',
        'user_id',
        'user_address',
        'totalAmount',
        'subTotal',
        'shipping',
        'tax',
        'discount',
        'status'
    ];
}
