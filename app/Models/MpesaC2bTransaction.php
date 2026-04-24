<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MpesaC2bTransaction extends Model
{
    protected $table = 'mpesa_c2b_transactions';

    protected $fillable = [
        'transaction_id',
        'phone',
        'amount',
        'bill_reference_number',
        'transaction_time',
        'status',
        'mpesa_receipt_number',
        'raw_payload',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'transaction_time' => 'datetime',
            'raw_payload' => 'array',
        ];
    }
}
