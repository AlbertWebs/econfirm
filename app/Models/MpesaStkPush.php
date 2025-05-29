<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MpesaStkPush extends Model
{
    protected $table = 'mpesa_stk_pushes';

    protected $fillable = [
        'phone',
        'amount',
        'reference',
        'description',
        'checkout_request_id',
        'merchant_request_id',
        'response_code',
        'response_description',
        'customer_message',
        'status',
        'callback_metadata',
    ];

    protected $casts = [
        'callback_metadata' => 'array',
        'amount' => 'decimal:2',
    ];
}
