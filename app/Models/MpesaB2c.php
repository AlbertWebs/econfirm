<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
        'approved_by_admin_id',
        'approved_at',
        'rejected_by_admin_id',
        'rejected_at',
        'rejection_reason',
        'source_transaction_id',
    ];

    protected $casts = [
        'raw_response' => 'array',
        'amount' => 'decimal:2',
        'approved_at' => 'datetime',
        'rejected_at' => 'datetime',
    ];

    public function approvedByAdmin(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'approved_by_admin_id');
    }

    public function rejectedByAdmin(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'rejected_by_admin_id');
    }

    public function isPending(): bool
    {
        return strcasecmp((string) $this->status, 'pending') === 0;
    }
}
