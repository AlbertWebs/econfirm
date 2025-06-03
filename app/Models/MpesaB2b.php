<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MpesaB2b extends Model
{
    protected $table = 'mpesa_b2b';

    protected $fillable = [
        'originator_conversation_id',
        'conversation_id',
        'transaction_id',
        'transaction_type',
        'party_a',
        'party_b',
        'amount',
        'result_code',
        'result_desc',
        'command_id',
        'initiator',
        'security_credential',
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
