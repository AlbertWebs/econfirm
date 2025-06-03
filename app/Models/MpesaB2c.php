<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MpesaB2c extends Model
{
    protected $table = 'mpesa_b2c';

    protected $fillable = [
        'originator_conversation_id',
        'conversation_id',
        'transaction_id',
        'transaction_type',
        'receiver_mobile',
        'amount',
        'result_code',
        'result_desc',
        'command_id',
        'initiator_name',
        'security_credential',
        'party_a',
        'party_b',
        'remarks',
        'occasion',
        'status',
        'raw_response',
    ];

    protected $casts = [
        'raw_response' => 'array',
        'amount' => 'decimal:2',
    ];
}
