<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MpesaStkPush extends Model
{
    protected $table = 'mpesa_stk_pushes';

    protected $fillable = [
        'initiator_ip',
        'phone',
        'amount',
        'reference',
        'description',
        'checkout_request_id',
        'merchant_request_id',
        'response_code',
        'response_description',
        'customer_message',
        'status',
        'result_desc',
        'callback_metadata',
    ];

    protected $casts = [
        'callback_metadata' => 'array',
        'amount' => 'decimal:2',
    ];

    /**
     * STK ResultCode 1032 = cancelled on phone; ResultDesc often contains "cancel" + "user".
     */
    public static function stkFailureLooksLikeUserCancelled(?string $resultDesc): bool
    {
        $d = trim((string) $resultDesc);
        if ($d === '') {
            return false;
        }
        if (preg_match('/\b1032\b/', $d)) {
            return true;
        }
        $lower = mb_strtolower($d, 'UTF-8');
        if (! str_contains($lower, 'cancel')) {
            return false;
        }

        return str_contains($lower, 'user')
            || str_contains($lower, 'customer')
            || str_contains($lower, 'subscriber');
    }

    /**
     * Admin M-PESA -> STK list: clearer than enum "Failed" when the payer cancelled on the handset.
     */
    public function adminDisplayStatus(): string
    {
        $status = trim((string) ($this->status ?? ''));
        if (strcasecmp($status, 'Failed') === 0 && self::stkFailureLooksLikeUserCancelled($this->result_desc)) {
            return 'Request cancelled by user';
        }

        return $status !== '' ? $status : 'Unknown';
    }

    public function adminStatusBadgeClass(?string $displayStatus = null): string
    {
        $label = $displayStatus ?? $this->adminDisplayStatus();
        if ($label === 'Success') {
            return 'bg-emerald-100 text-emerald-800 ring-1 ring-emerald-200';
        }
        if ($label === 'Request cancelled by user') {
            return 'bg-amber-100 text-amber-950 ring-1 ring-amber-200';
        }
        if (strcasecmp((string) ($this->status ?? ''), 'Failed') === 0) {
            return 'bg-rose-100 text-rose-800 ring-1 ring-rose-200';
        }
        if (strcasecmp((string) ($this->status ?? ''), 'Pending') === 0) {
            return 'bg-slate-100 text-slate-700 ring-1 ring-slate-200';
        }

        return 'bg-slate-100 text-slate-700 ring-1 ring-slate-200';
    }
}
