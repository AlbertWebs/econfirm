<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'transaction_type',
        'transaction_amount',
        'sender_mobile',
        'receiver_mobile',
        'transaction_details',
        'status',
    ];
}
