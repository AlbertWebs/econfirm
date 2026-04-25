<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VelipayPayment extends Model
{
    protected $fillable = [
        'transaction_id',
        'velipay_payment_id',
        'initiator_ip',
        'phone',
        'amount',
        'merchant_reference',
        'description',
        'status',
        'failure_reason',
        'receipt_number',
        'raw_response',
        'webhook_payload',
    ];

    protected $casts = [
        'raw_response' => 'array',
        'webhook_payload' => 'array',
    ];

    public function transaction()
    {
        return $this->belongsTo(Transaction::class, 'transaction_id', 'transaction_id');
    }
}
