<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\VelipayPayment;
use App\Services\EscrowVelipayFundingService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class VelipayWebhookController extends Controller
{
    public function handle(Request $request): Response
    {
        $payload = $request->getContent();
        if (! $this->isWebhookAuthorized($request, $payload)) {
            return response('Unauthorized', 401);
        }

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

            if ($event === 'payment.payout_success') {
                $payment->status = 'payout_success';
                $payment->receipt_number = (string) ($data['payouts'][0]['receiptNumber'] ?? $payment->receipt_number);
                $payment->save();

                if ($payment->transaction && strcasecmp((string) $payment->transaction->status, 'Completed') !== 0) {
                    $payment->transaction->status = 'Completed';
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

    private function isWebhookAuthorized(Request $request, string $payload): bool
    {
        $secret = (string) config('velipay.webhook_secret', '');
        if ($secret === '') {
            return true;
        }

        $provided = trim((string) (
            $request->header('X-VeliPay-Signature')
            ?? $request->header('X-Webhook-Signature')
            ?? $request->header('X-Signature')
            ?? ''
        ));

        if ($provided === '') {
            Log::warning('VeliPay webhook missing signature header');

            return false;
        }

        $expected = hash_hmac('sha256', $payload, $secret);
        $normalized = Str::startsWith(strtolower($provided), 'sha256=')
            ? substr($provided, 7)
            : $provided;

        if (! hash_equals($expected, trim($normalized))) {
            Log::warning('VeliPay webhook signature mismatch');

            return false;
        }

        return true;
    }
}
