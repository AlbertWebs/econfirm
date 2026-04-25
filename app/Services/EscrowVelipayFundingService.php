<?php

namespace App\Services;

use App\Models\Transaction;
use App\Models\VelipayPayment;
use Illuminate\Support\Facades\Log;

class EscrowVelipayFundingService
{
    public static function markFundedByPayment(VelipayPayment $payment): ?Transaction
    {
        $transactionId = trim((string) ($payment->transaction_id ?? ''));
        if ($transactionId === '') {
            return null;
        }

        $transaction = Transaction::where('transaction_id', $transactionId)->first();
        if (! $transaction) {
            Log::warning('VeliPay paid event has no matching transaction', [
                'payment_id' => $payment->velipay_payment_id,
                'transaction_id' => $transactionId,
            ]);

            return null;
        }

        $alreadyFunded = in_array((string) $transaction->status, ['Escrow Funded', 'Completed'], true);
        if (! $alreadyFunded) {
            $transaction->status = 'Escrow Funded';
            $transaction->save();

            try {
                (new SmsService)->notifyEscrowFunded($transaction->fresh());
            } catch (\Throwable $e) {
                Log::error('Escrow funded SMS failed after VeliPay paid event', [
                    'transaction_id' => $transaction->transaction_id,
                    'payment_id' => $payment->velipay_payment_id,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        return $transaction->fresh();
    }
}
