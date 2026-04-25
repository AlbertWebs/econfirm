<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\VelipayPayment;
use App\Services\EscrowVelipayFundingService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class VelipayWebhookController extends Controller
{
    public function handle(Request $request): Response
    {
        $payload = $request->getContent();
        $body = json_decode($payload, true);

        if (! is_array($body)) {
            return response('Bad request', 400);
        }

        $event = (string) ($body['event'] ?? '');
        $data = is_array($body['data'] ?? null) ? $body['data'] : [];
        $paymentId = trim((string) ($data['paymentId'] ?? ''));

        if ($paymentId === '') {
            Log::warning('VeliPay webhook missing paymentId', ['event' => $event, 'body' => $body]);

            return response('Bad request', 400);
        }

        DB::transaction(function () use ($event, $data, $body, $paymentId): void {
            $payment = VelipayPayment::where('velipay_payment_id', $paymentId)->lockForUpdate()->first();
            if (! $payment) {
                Log::warning('VeliPay webhook for unknown payment', [
                    'event' => $event,
                    'payment_id' => $paymentId,
                ]);

                return;
            }

            $payment->webhook_payload = $body;

            if ($event === 'payment.failed') {
                $payment->status = 'failed';
                $payment->failure_reason = (string) ($data['status'] ?? 'failed');
                $payment->save();

                if ($payment->transaction) {
                    $payment->transaction->status = 'stk_failed';
                    $payment->transaction->save();
                }

                return;
            }

            if ($event === 'payment.paid' || $event === 'payment.settled') {
                $payment->status = 'paid';
                $payment->receipt_number = (string) ($data['receiptNumber'] ?? $payment->receipt_number);
                $payment->save();

                EscrowVelipayFundingService::markFundedByPayment($payment);

                return;
            }

            // Keep a trail for non-terminal events such as payout_success.
            $payment->status = (string) ($data['status'] ?? $payment->status ?: 'pending');
            $payment->save();
        });

        return response('OK', 200);
    }
}
