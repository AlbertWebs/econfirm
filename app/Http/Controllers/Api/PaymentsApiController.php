<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Services\PaymentGatewayAuditLog;
use App\Services\SmsService;
use App\Services\StkRequestIpLimiter;
use App\Services\VelipayService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class PaymentsApiController extends Controller
{
    /**
     * POST /api/v1/payments/stk-push
     *
     * Initiates STK Push for an escrow row owned by the API key.
     */
    public function stkPush(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'transaction_id' => 'required|string',
            'payer_phone' => 'required|string',
        ]);

        if ($validator->fails()) {
            PaymentGatewayAuditLog::record('stk_push.validation_failed', $request, ['errors' => $validator->errors()->toArray()]);

            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $apiUser = $request->get('api_user');
        $transaction = Transaction::query()
            ->where('transaction_id', (string) $request->input('transaction_id'))
            ->where('api_user_id', $apiUser->id)
            ->first();

        if (! $transaction) {
            PaymentGatewayAuditLog::record('stk_push.not_found', $request, ['transaction_id' => (string) $request->input('transaction_id')]);

            return response()->json([
                'success' => false,
                'message' => 'Transaction not found',
            ], 404);
        }

        if (strcasecmp((string) $transaction->status, 'pending') !== 0) {
            PaymentGatewayAuditLog::record('stk_push.invalid_status', $request, [
                'transaction_id' => $transaction->transaction_id,
                'status' => $transaction->status,
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Transaction must be in pending status to start funding.',
            ], 400);
        }

        $phone = $this->normalizeKenyaMsisdn((string) $request->input('payer_phone'));
        if ($phone === null) {
            PaymentGatewayAuditLog::record('stk_push.invalid_phone', $request, []);

            return response()->json([
                'success' => false,
                'message' => 'payer_phone must be a Kenya number (2547XXXXXXXX or 07XXXXXXXX).',
            ], 422);
        }

        $transaction->sender_mobile = $phone;
        if ((float) ($transaction->transaction_fee ?? 0) <= 0) {
            $transaction->transaction_fee = round((float) $transaction->transaction_amount * 0.01, 2);
        }
        $transaction->save();

        $clientIp = $request->ip();
        if (StkRequestIpLimiter::isBlocked($clientIp)) {
            PaymentGatewayAuditLog::record('stk_push.rate_limited_ip', $request, ['transaction_id' => $transaction->transaction_id]);

            return response()->json([
                'success' => false,
                'message' => StkRequestIpLimiter::MESSAGE,
            ], 429);
        }

        $velipay = new VelipayService;
        $velipayResponse = $velipay->stkPush($transaction, $clientIp);

        PaymentGatewayAuditLog::record(
            $velipayResponse['success'] ? 'stk_push.initiated' : 'stk_push.failed',
            $request,
            [
                'transaction_id' => $transaction->transaction_id,
                'success' => $velipayResponse['success'],
            ]
        );

        if ($velipayResponse['success']) {
            $paymentId = (string) (($velipayResponse['data']['paymentId'] ?? '') ?: '');
            $transaction->status = 'stk_initiated';
            $transaction->checkout_request_id = $paymentId !== '' ? $paymentId : null;
            $transaction->merchant_request_id = null;
            $transaction->save();

            try {
                (new SmsService)->notifyEscrowStkInitiated($transaction->fresh());
            } catch (\Throwable $e) {
                Log::error('Escrow STK initiation SMS failed (payments API)', [
                    'transaction_id' => $transaction->transaction_id,
                    'error' => $e->getMessage(),
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Payment request sent. The payer should confirm on their handset.',
                'data' => [
                    'checkout_request_id' => $transaction->checkout_request_id,
                    'merchant_request_id' => $transaction->merchant_request_id,
                    'status' => $transaction->status,
                ],
            ]);
        }

        $transaction->status = 'stk_failed';
        $transaction->save();

        return response()->json([
            'success' => false,
            'message' => $velipayResponse['message'] ?? 'Payment initiation failed.',
        ], 502);
    }

    public function c2b(Request $request): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => 'Endpoint deprecated. Use /api/v1/payments/stk-push with VeliPay.',
        ], 410);
    }

    public function b2c(Request $request): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => 'Endpoint deprecated. Withdraw flows are handled directly by VeliPay business APIs.',
        ], 410);
    }

    public function b2b(Request $request): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => 'Endpoint deprecated. Withdraw flows are handled directly by VeliPay business APIs.',
        ], 410);
    }

    public function reversal(Request $request, string $transactionId): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => 'Endpoint deprecated. Reversal flow is not available in eConfirm VeliPay integration.',
        ], 410);
    }

    protected function statusAllowsPayout(?string $status): bool
    {
        $s = strtolower(trim((string) $status));

        return $s === 'escrow funded'
            || $s === 'funded'
            || $s === 'in_progress';
    }

    protected function normalizeKenyaMsisdn(string $value): ?string
    {
        $raw = preg_replace('/\s+/', '', (string) $value);
        if ($raw === '') {
            return null;
        }
        if (str_starts_with($raw, '+')) {
            $raw = substr($raw, 1);
        }
        if (str_starts_with($raw, '0')) {
            $raw = '254'.substr($raw, 1);
        }
        if (! str_starts_with($raw, '254')) {
            $raw = '254'.$raw;
        }
        if (! preg_match('/^254\d{9}$/', $raw)) {
            return null;
        }

        return $raw;
    }
}
