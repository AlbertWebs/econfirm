<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MpesaB2cCallback extends Model
{
    protected $table = 'mpesa_b2c_callbacks';

    protected $fillable = [
        'conversation_id',
        'originator_conversation_id',
        'transaction_id',
        'result_type',
        'result_code',
        'result_desc',
        'transaction_amount',
        'transaction_receipt',
        'receiver_party_public_name',
        'transaction_completed_datetime',
        'b2c_working_account_available_funds',
        'b2c_utility_account_available_funds',
        'b2c_charges_paid_account_available_funds',
        'receiver_is_registered_customer',
        'charges_paid',
        'queue_timeout_url',
        'raw_callback',
    ];

    protected $casts = [
        'raw_callback' => 'array',
    ];
}
