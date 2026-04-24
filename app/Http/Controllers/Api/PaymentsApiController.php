<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Services\MpesaService;
use App\Services\PaymentGatewayAuditLog;
use App\Services\SmsService;
use App\Services\StkRequestIpLimiter;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class PaymentsApiController extends Controller
{
    /**
     * POST /api/v1/payments/stk-push
     *
     * Initiates STK Push for an escrow row owned by the API key (M-Pesa call is server-side only).
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

        $mpesa = new MpesaService;
        $mpesaResponse = $mpesa->stkPush($transaction, $clientIp);

        PaymentGatewayAuditLog::record(
            $mpesaResponse['success'] ? 'stk_push.initiated' : 'stk_push.failed',
            $request,
            [
                'transaction_id' => $transaction->transaction_id,
                'success' => $mpesaResponse['success'],
            ]
        );

        if ($mpesaResponse['success']) {
            $transaction->status = 'stk_initiated';
            $transaction->checkout_request_id = $mpesaResponse['data']['CheckoutRequestID'] ?? null;
            $transaction->merchant_request_id = $mpesaResponse['data']['MerchantRequestID'] ?? null;
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
            'message' => $mpesaResponse['message'] ?? 'STK initiation failed.',
        ], 502);
    }

    /**
     * POST /api/v1/payments/c2b
     *
     * Optional sandbox C2B simulate — still executed only on our servers; disabled in production unless configured.
     */
    public function c2b(Request $request): JsonResponse
    {
        if (app()->environment('production') && ! config('mpesa.allow_partner_c2b_simulate')) {
            return response()->json([
                'success' => false,
                'message' => 'C2B simulation is not enabled for production API keys. All live C2B funds flow through our registered paybills and internal processing.',
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'amount' => 'required|numeric|min:1',
            'msisdn' => 'required|string',
            'bill_reference' => 'nullable|string|max:64',
        ]);

        if ($validator->fails()) {
            PaymentGatewayAuditLog::record('c2b.validation_failed', $request, ['errors' => $validator->errors()->toArray()]);

            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $phone = $this->normalizeKenyaMsisdn((string) $request->input('msisdn'));
        if ($phone === null) {
            return response()->json([
                'success' => false,
                'message' => 'msisdn must be a Kenya number in international format.',
            ], 422);
        }

        $billRef = $request->input('bill_reference');
        if (is_string($billRef) && $billRef !== '') {
            $owned = Transaction::query()
                ->where('transaction_id', $billRef)
                ->where('api_user_id', $request->get('api_user')->id)
                ->exists();
            if (! $owned) {
                PaymentGatewayAuditLog::record('c2b.bill_reference_not_owned', $request, ['bill_reference' => $billRef]);

                return response()->json([
                    'success' => false,
                    'message' => 'bill_reference must match a transaction_id owned by this API key.',
                ], 422);
            }
        }

        $mpesa = new MpesaService;
        $raw = $mpesa->c2b([
            'amount' => (float) $request->input('amount'),
            'msisdn' => $phone,
            'reference' => is_string($billRef) && $billRef !== '' ? $billRef : 'API',
        ]);

        PaymentGatewayAuditLog::record('c2b.simulate', $request, [
            'amount' => $request->input('amount'),
            'msisdn_tail' => substr($phone, -4),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'C2B simulate request processed by our platform (M-Pesa traffic is server-side only).',
            'data' => is_array($raw) ? $raw : ['result' => $raw],
        ]);
    }

    /**
     * POST /api/v1/payments/b2c
     */
    public function b2c(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'transaction_id' => 'required|string',
        ]);

        if ($validator->fails()) {
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
            return response()->json(['success' => false, 'message' => 'Transaction not found'], 404);
        }

        if (! $this->statusAllowsPayout($transaction->status)) {
            PaymentGatewayAuditLog::record('b2c.invalid_status', $request, [
                'transaction_id' => $transaction->transaction_id,
                'status' => $transaction->status,
            ]);

            return response()->json([
                'success' => false,
                'message' => 'B2C payout is only available once the escrow is funded and eligible for disbursement.',
            ], 400);
        }

        $mpesa = new MpesaService;
        $result = $mpesa->b2c($transaction);

        PaymentGatewayAuditLog::record($result['success'] ? 'b2c.initiated' : 'b2c.failed', $request, [
            'transaction_id' => $transaction->transaction_id,
            'success' => $result['success'],
        ]);

        if ($result['success']) {
            return response()->json([
                'success' => true,
                'message' => $result['message'] ?? 'B2C initiated; final status arrives via our platform webhooks.',
                'data' => $result['data'] ?? null,
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => $result['message'] ?? 'B2C initiation failed.',
        ], 502);
    }

    /**
     * POST /api/v1/payments/b2b
     */
    public function b2b(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'transaction_id' => 'required|string',
        ]);

        if ($validator->fails()) {
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
            return response()->json(['success' => false, 'message' => 'Transaction not found'], 404);
        }

        if (! $this->statusAllowsPayout($transaction->status)) {
            return response()->json([
                'success' => false,
                'message' => 'B2B transfer is only available when the escrow is funded and configured for paybill/till payout.',
            ], 400);
        }

        $till = trim((string) ($transaction->paybill_till_number ?? ''));
        if ($till === '') {
            return response()->json([
                'success' => false,
                'message' => 'This transaction has no paybill_till_number; set it before requesting B2B.',
            ], 422);
        }

        $mpesa = new MpesaService;
        $result = $mpesa->b2b($transaction);

        PaymentGatewayAuditLog::record($result['success'] ? 'b2b.initiated' : 'b2b.failed', $request, [
            'transaction_id' => $transaction->transaction_id,
            'success' => $result['success'],
        ]);

        if ($result['success']) {
            return response()->json([
                'success' => true,
                'message' => $result['message'] ?? 'B2B initiated; final status is processed on our servers.',
                'data' => $result['data'] ?? null,
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => $result['message'] ?? 'B2B initiation failed.',
        ], 502);
    }

    /**
     * POST /api/v1/transactions/{transaction_id}/reversal
     */
    public function reversal(Request $request, string $transactionId): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'mpesa_transaction_id' => 'required|string|max:64',
            'amount' => 'required|numeric|min:1',
            'remarks' => 'nullable|string|max:100',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $apiUser = $request->get('api_user');
        $transaction = Transaction::query()
            ->where('transaction_id', $transactionId)
            ->where('api_user_id', $apiUser->id)
            ->first();

        if (! $transaction) {
            return response()->json(['success' => false, 'message' => 'Transaction not found'], 404);
        }

        $mpesa = new MpesaService;
        $result = $mpesa->requestTransactionReversal(
            (string) $request->input('mpesa_transaction_id'),
            (float) $request->input('amount'),
            (string) $request->input('remarks', 'Reversal')
        );

        PaymentGatewayAuditLog::record($result['success'] ? 'reversal.requested' : 'reversal.failed', $request, [
            'transaction_id' => $transaction->transaction_id,
            'mpesa_transaction_id' => (string) $request->input('mpesa_transaction_id'),
            'success' => $result['success'],
        ]);

        if ($result['success']) {
            return response()->json([
                'success' => true,
                'message' => $result['message'] ?? 'Reversal queued.',
                'data' => $result['data'] ?? null,
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => $result['message'] ?? 'Reversal failed.',
            'data' => $result['data'] ?? null,
        ], 502);
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
