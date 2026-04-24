<?php

namespace App\Services;

use App\Models\MpesaStkPush;
use App\Models\Transaction;
use Illuminate\Support\Facades\Log;

class EscrowStkFundingService
{
    /**
     * Resolve escrow row from STK row: checkout_request_id on transactions first, then AccountReference (mpesa_stk_pushes.reference).
     */
    public static function findTransactionForStk(MpesaStkPush $stk): ?Transaction
    {
        $cid = trim((string) ($stk->checkout_request_id ?? ''));
        if ($cid !== '') {
            $byCheckout = Transaction::where('checkout_request_id', $cid)->first();
            if ($byCheckout) {
                return $byCheckout;
            }
        }

        $ref = trim((string) ($stk->reference ?? ''));
        if ($ref !== '') {
            return Transaction::where('transaction_id', $ref)->first();
        }

        return null;
    }

    /**
     * True when CallbackMetadata holds at least one Item with a Name (handles Daraja list or single item, or { "Item": [...] }).
     */
    public static function callbackMetadataHasUsableItems(mixed $raw): bool
    {
        if (! is_array($raw) || $raw === []) {
            return false;
        }

        if (isset($raw['Item']) && is_array($raw['Item'])) {
            $raw = $raw['Item'];
        }

        if (isset($raw['Name'])) {
            return true;
        }

        foreach ($raw as $row) {
            if (is_array($row) && isset($row['Name'])) {
                return true;
            }
        }

        return false;
    }

    /**
     * Mark linked transaction as Escrow Funded and notify parties (idempotent).
     */
    public static function markFundedIfNotAlready(MpesaStkPush $stk): ?Transaction
    {
        $transaction = self::findTransactionForStk($stk);
        if (! $transaction) {
            Log::error('STK Success but no matching escrow transaction', [
                'checkout_request_id' => $stk->checkout_request_id,
                'reference' => $stk->reference,
            ]);

            return null;
        }

        $alreadyFunded = in_array($transaction->status, ['Escrow Funded', 'Completed'], true);
        if (! $alreadyFunded) {
            $transaction->status = 'Escrow Funded';
            $transaction->save();

            try {
                (new SmsService)->notifyEscrowFunded($transaction->fresh());
            } catch (\Throwable $e) {
                Log::error('Escrow funded SMS failed', [
                    'transaction_id' => $transaction->transaction_id,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        return $transaction->fresh();
    }
}
