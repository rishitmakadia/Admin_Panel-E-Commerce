<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $table = 'payment';
    protected $fillable = [
        'razorpay_order_id',
        'payment_id',
        'amount',
        'currency',
        'receipt',
        'signature',
        'status',
        'created_at',
    ];
}
