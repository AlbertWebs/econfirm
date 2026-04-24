<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property-read Transaction|null $sourceEscrow
 */
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

    public function sourceEscrow(): BelongsTo
    {
        return $this->belongsTo(Transaction::class, 'source_transaction_id', 'transaction_id');
    }

    public function displayAmountKes(): float
    {
        $a = (float) ($this->amount ?? 0);
        if ($a > 0) {
            return $a;
        }
        $raw = $this->raw_response;
        if (is_array($raw)) {
            foreach (['Amount', 'amount'] as $k) {
                if (isset($raw[$k]) && is_numeric($raw[$k]) && (float) $raw[$k] > 0) {
                    return (float) $raw[$k];
                }
            }
        }
        if ($this->relationLoaded('sourceEscrow') && $this->sourceEscrow) {
            return (float) $this->sourceEscrow->transaction_amount;
        }
        if ($this->source_transaction_id) {
            $v = Transaction::where('transaction_id', $this->source_transaction_id)->value('transaction_amount');

            return $v !== null ? (float) $v : 0.0;
        }

        return 0.0;
    }

    public static function sumEffectiveAmountKes(): float
    {
        return (float) (static::query()
            ->leftJoin('transactions', 'mpesa_b2b.source_transaction_id', '=', 'transactions.transaction_id')
            ->selectRaw('COALESCE(SUM(COALESCE(NULLIF(mpesa_b2b.amount, 0), transactions.transaction_amount, 0)), 0) as aggregate')
            ->value('aggregate') ?? 0);
    }

    public function isPending(): bool
    {
        return strcasecmp((string) $this->status, 'pending') === 0;
    }
}
