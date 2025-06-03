<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MpesaB2bCallback extends Model
{
    protected $table = 'mpesa_b2b_callbacks';

    protected $fillable = [
        'conversation_id',
        'originator_conversation_id',
        'transaction_id',
        'result_type',
        'result_code',
        'result_desc',
        'command_id',
        'receiver_party_public_name',
        'amount',
        'debit_account_balance',
        'party_a',
        'party_b',
        'transaction_receipt',
        'transaction_completed_datetime',
        'initiator_account_current_balance',
        'charges_paid',
        'currency',
        'receiver_party',
        'transaction_date',
        'b2b_channel',
        'raw_callback',
    ];

    protected $casts = [
        'raw_callback' => 'array',
    ];
}
