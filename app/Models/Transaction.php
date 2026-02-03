<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'otp',
        'transaction_fee',
        'paybill_till_number',
        'payment_method',
        'transaction_id',
        'transaction_type',
        'transaction_amount',
        'sender_mobile',
        'receiver_mobile',
        'transaction_details',
        'status',
        'buyer_email',
        'seller_email',
        'currency',
        'terms',
        'confirmation_code',
    ];
}
