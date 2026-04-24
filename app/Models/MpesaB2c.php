<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

/**
 * @property-read Transaction|null $sourceEscrow
 */
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

    /**
     * Escrow row referenced by source_transaction_id (same string as transactions.transaction_id).
     */
    public function sourceEscrow(): BelongsTo
    {
        return $this->belongsTo(Transaction::class, 'source_transaction_id', 'transaction_id');
    }

    /**
     * KES amount for admin UI: stored column, else Daraja response snapshot, else linked escrow principal.
     */
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

    /**
     * Total KES for dashboard cards (handles legacy rows with amount 0 before fix).
     */
    public static function sumEffectiveAmountKes(): float
    {
        return (float) (static::query()
            ->leftJoin('transactions', 'mpesa_b2c.source_transaction_id', '=', 'transactions.transaction_id')
            ->selectRaw('COALESCE(SUM(COALESCE(NULLIF(mpesa_b2c.amount, 0), transactions.transaction_amount, 0)), 0) as aggregate')
            ->value('aggregate') ?? 0);
    }

    public function isPending(): bool
    {
        return strcasecmp((string) $this->status, 'pending') === 0;
    }

    /**
     * Admin list/detail: human payout state using row + optional pre-hydrated adminLatestCallback relation.
     *
     * @return array{label: string, sublabel: string, badge_class: string}
     */
    public function adminPayoutOutcome(): array
    {
        if ($this->rejected_at) {
            return [
                'label' => 'Rejected (admin)',
                'sublabel' => Str::limit(trim((string) ($this->rejection_reason ?? '')), 96) ?: '—',
                'badge_class' => 'bg-rose-100 text-rose-900 ring-1 ring-rose-200',
            ];
        }

        if ($this->isPending() && empty($this->originator_conversation_id)) {
            return [
                'label' => 'Awaiting admin',
                'sublabel' => 'Not submitted to Safaricom yet',
                'badge_class' => 'bg-amber-100 text-amber-950 ring-1 ring-amber-200',
            ];
        }

        $cb = $this->relationLoaded('adminLatestCallback') ? $this->getRelation('adminLatestCallback') : null;
        if ($cb instanceof MpesaB2cCallback) {
            $rc = (int) ($cb->result_code ?? 1);
            if ($rc === 0) {
                $parts = array_filter([
                    $cb->transaction_receipt ? 'Receipt: '.$cb->transaction_receipt : null,
                    $cb->result_desc ? (string) $cb->result_desc : null,
                ]);

                return [
                    'label' => 'Paid (M-Pesa)',
                    'sublabel' => Str::limit(implode(' · ', $parts) ?: 'Result code 0', 120),
                    'badge_class' => 'bg-emerald-100 text-emerald-900 ring-1 ring-emerald-200',
                ];
            }

            return [
                'label' => 'Failed (M-Pesa)',
                'sublabel' => Str::limit(trim((string) ($cb->result_desc ?? 'Result '.$cb->result_code)), 120),
                'badge_class' => 'bg-rose-100 text-rose-900 ring-1 ring-rose-200',
            ];
        }

        if (! empty($this->originator_conversation_id) || ! empty($this->conversation_id)) {
            return [
                'label' => 'Awaiting result',
                'sublabel' => 'Sent to Safaricom; no callback row stored yet',
                'badge_class' => 'bg-sky-100 text-sky-950 ring-1 ring-sky-200',
            ];
        }

        return [
            'label' => (string) ($this->status ?: 'Unknown'),
            'sublabel' => Str::limit(trim((string) ($this->result_desc ?? '')), 96) ?: '—',
            'badge_class' => 'bg-slate-100 text-slate-800 ring-1 ring-slate-200',
        ];
    }
}
