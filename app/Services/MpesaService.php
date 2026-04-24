<?php

namespace App\Services;

use App\Models\MpesaB2b;
use App\Models\MpesaB2c;
use App\Models\MpesaStkPush;
use App\Models\Transaction;
use Illuminate\Support\Facades\Http;

class MpesaService
{
    protected function stkQueryEndpoint(): string
    {
        $configured = trim((string) config('mpesa.stk_query_url', ''));
        if ($configured !== '') {
            return $configured;
        }

        $stkPushUrl = (string) config('mpesa.stk_url', 'https://sandbox.safaricom.co.ke/mpesa/stkpush/v1/processrequest');
        if (str_contains($stkPushUrl, '/mpesa/stkpush/v1/processrequest')) {
            return str_replace('/mpesa/stkpush/v1/processrequest', '/mpesa/stkpushquery/v1/query', $stkPushUrl);
        }

        return 'https://sandbox.safaricom.co.ke/mpesa/stkpushquery/v1/query';
    }

    /**
     * Daraja ResultURL / QueueTimeOutURL must match real routes (see routes/api.php).
     */
    protected function b2cResultUrl(): string
    {
        $u = trim((string) config('mpesa.b2c_result_url', ''));

        return $u !== '' ? $u : url('/api/mpesa/b2c/callback');
    }

    protected function b2cTimeoutUrl(): string
    {
        $u = trim((string) config('mpesa.b2c_timeout_url', ''));

        return $u !== '' ? $u : url('/api/mpesa/b2c/timeout');
    }

    /**
     * Guzzle SSL options: fixes cURL error 60 on Windows when php.ini has no CA bundle.
     */
    protected function mpesaHttp(): \Illuminate\Http\Client\PendingRequest
    {
        $opts = [];
        if (! filter_var(config('mpesa.verify_ssl', true), FILTER_VALIDATE_BOOLEAN)) {
            $opts['verify'] = false;
        } else {
            $ca = config('mpesa.ca_bundle');
            if (is_string($ca) && $ca !== '' && is_file($ca)) {
                $opts['verify'] = $ca;
            }
        }

        return $opts === [] ? Http::withOptions([]) : Http::withOptions($opts);
    }

    /**
     * Total KES to charge on STK: escrow principal + platform fee (whole shillings).
     */
    protected function stkChargeAmountKes(Transaction $transaction): int
    {
        $principal = (float) $transaction->transaction_amount;
        $fee = (float) ($transaction->transaction_fee ?? 0);

        return (int) round($principal + $fee, 0);
    }

    /**
     * Initiate M-Pesa STK Push
     *
     * @param  string|null  $initiatorIp  Client IP (stored on mpesa_stk_pushes for per-IP limits)
     */
    public function stkPush(Transaction $transaction, ?string $initiatorIp = null): array
    {
        $timestamp = now()->format('YmdHis');
        $password = base64_encode(config('mpesa.shortcode').config('mpesa.passkey').$timestamp);

        $amountKes = $this->stkChargeAmountKes($transaction);

        $payload = [
            'BusinessShortCode' => config('mpesa.shortcode'),
            'Password' => $password,
            'Timestamp' => $timestamp,
            'TransactionType' => 'CustomerPayBillOnline',
            'Amount' => $amountKes,
            'PartyA' => preg_replace('/[\s+]/', '', $transaction->sender_mobile),
            'PartyB' => config('mpesa.shortcode'),
            'PhoneNumber' => preg_replace('/[\s+]/', '', $transaction->sender_mobile),
            'CallBackURL' => config('mpesa.callback_url'), // Ensure correct path
            'AccountReference' => $transaction->transaction_id,
            'TransactionDesc' => $transaction->transaction_details ?? 'Escrow Payment',
        ];

        $callbackUrl = (string) ($payload['CallBackURL'] ?? '');
        if ($callbackUrl !== '' && (
            ! str_starts_with($callbackUrl, 'https://')
            || str_contains($callbackUrl, 'localhost')
            || str_contains($callbackUrl, '127.0.0.1')
        )) {
            \Log::warning('M-Pesa STK: CallBackURL will be rejected by Daraja (public HTTPS required)', [
                'CallBackURL' => $callbackUrl,
                'transaction_id' => $transaction->transaction_id,
            ]);
        }

        // Log Payload
        \Log::info('M-Pesa STK Push Payload', [
            'payload' => $payload,
            'transaction_id' => $transaction->transaction_id,
            'principal_kes' => $transaction->transaction_amount,
            'fee_kes' => $transaction->transaction_fee ?? 0,
            'stk_total_kes' => $amountKes,
        ]);

        $endpoint = config('mpesa.stk_url', 'https://sandbox.safaricom.co.ke/mpesa/stkpush/v1/processrequest');
        $token = $this->getAccessToken();
        if ($token === '') {
            \Log::error('M-Pesa STK Push aborted: OAuth token empty', [
                'transaction_id' => $transaction->transaction_id,
                'endpoint' => $endpoint,
            ]);

            return [
                'success' => false,
                'message' => 'M-Pesa authentication failed. Check consumer key/secret and network, then try again.',
                'data' => null,
            ];
        }

        try {
            $response = $this->mpesaHttp()->timeout(60)
                ->withToken($token)
                ->acceptJson()
                ->post($endpoint, $payload);
        } catch (\Throwable $e) {
            \Log::error('M-Pesa STK Push HTTP exception', [
                'transaction_id' => $transaction->transaction_id,
                'exception_class' => $e::class,
                'message' => $e->getMessage(),
                'code' => $e->getCode(),
                'trace' => $e->getTraceAsString(),
            ]);

            return [
                'success' => false,
                'message' => 'STK request failed: '.$e->getMessage(),
                'data' => null,
            ];
        }

        $status = $response->status();
        $body = $response->json();
        $raw = $response->body();

        $responseCode = is_array($body) ? ($body['ResponseCode'] ?? null) : null;
        $responseCodeOk = is_array($body) && (string) ($body['ResponseCode'] ?? '') === '0';
        $customerMessage = is_array($body) ? ($body['CustomerMessage'] ?? $body['errorMessage'] ?? $body['error_description'] ?? null) : null;

        if ($response->successful() && $responseCodeOk) {
            // Save CheckoutRequestID and MerchantRequestID
            $MpesaStkPush = new MpesaStkPush;
            $MpesaStkPush->initiator_ip = $initiatorIp;
            $MpesaStkPush->phone = $transaction->sender_mobile;
            $MpesaStkPush->amount = $this->stkChargeAmountKes($transaction);
            $MpesaStkPush->reference = $transaction->transaction_id;
            $MpesaStkPush->description = $transaction->transaction_details ?? 'Escrow Payment';
            $MpesaStkPush->status = 'Pending';
            $MpesaStkPush->checkout_request_id = $body['CheckoutRequestID'] ?? null;
            $MpesaStkPush->merchant_request_id = $body['MerchantRequestID'] ?? null;
            $MpesaStkPush->save();

            return [
                'success' => true,
                'message' => 'STK push initiated.',
                'data' => $body,
            ];
        }

        \Log::error('M-Pesa STK Push failed (API or business error)', [
            'transaction_id' => $transaction->transaction_id,
            'http_status' => $status,
            'response_ok' => $response->successful(),
            'response_code' => $responseCode,
            'response_code_ok' => $responseCodeOk,
            'customer_message' => $customerMessage,
            'parsed_body' => $body,
            'raw_body' => $raw,
            'request_id' => $response->header('X-Request-ID') ?? $response->header('request-id'),
        ]);

        $errorCode = is_array($body) ? ($body['errorCode'] ?? null) : null;

        $userMessage = $customerMessage
            ?? (is_array($body) ? ($body['errorMessage'] ?? null) : null)
            ?? 'STK push failed.';

        if ($errorCode === '400.002.02'
            || (is_string($userMessage) && str_contains($userMessage, 'Invalid CallBackURL'))) {
            $userMessage = 'Invalid CallBackURL: Safaricom requires a public HTTPS URL registered in the Developer Portal. '
                .'Set MPESA_CALLBACK_URL in .env (e.g. https://your-tunnel.ngrok-free.app/api/mpesa/callback); http://localhost is not accepted.';
        }

        return [
            'success' => false,
            'message' => $userMessage,
            'data' => is_array($body) ? $body : ['raw' => $raw],
        ];
    }

    /**
     * Initiate B2B Payment (Business to Business)
     */
    public function b2b(Transaction $transaction): array
    {
        $endpoint = config('mpesa.b2b_url', 'https://sandbox.safaricom.co.ke/mpesa/b2b/v1/payment/request');
        $token = $this->getAccessToken();
        $payload = [
            'Initiator' => config('mpesa.initiator'),
            'SecurityCredential' => config('mpesa.security_credential'),
            'CommandID' => 'BusinessPayBill',
            'SenderIdentifierType' => '4',
            'RecieverIdentifierType' => '4',
            'Amount' => round($transaction['transaction_amount']),
            'PartyA' => config('mpesa.business_shortcode'),
            'PartyB' => $transaction['paybill_till_number'],
            'Remarks' => $transaction['transaction_details'],
            'AccountReference' => $transaction['transaction_id'],
            'Requester' => '254708374149',
            'QueueTimeOutURL' => config('mpesa.b2b_queue_timeout_url'),
            'ResultURL' => config('mpesa.b2b_results_url'),
            'Occasion' => $transaction['transaction_type'],
        ];
        // dd($payload);
        $response = $this->mpesaHttp()->withToken($token)->acceptJson()->post($endpoint, $payload);

        if ($response->successful() && isset($response['ResponseCode']) && $response['ResponseCode'] == '0') {
            $responseData = $response->json();
            // Log the response for debugging
            \Log::info('M-Pesa B2B Payment Response', [
                'response' => $responseData,
                'transaction_id' => $transaction->transaction_id,
            ]);
            // Fill the M-Pesa B2B table
            $mpesaB2b = new \App\Models\MpesaB2b;
            $mpesaB2b->originator_conversation_id = $responseData['OriginatorConversationID'] ?? null;
            $mpesaB2b->conversation_id = $responseData['ConversationID'] ?? null;
            $mpesaB2b->transaction_id = $responseData['TransactionID'] ?? null;
            $mpesaB2b->transaction_type = $responseData['TransactionType'] ?? null;
            $mpesaB2b->party_a = $responseData['PartyA'] ?? null;
            $mpesaB2b->party_b = $responseData['PartyB'] ?? null;
            $requestedB2bAmount = (float) round((float) $transaction->transaction_amount, 0);
            $mpesaB2b->amount = isset($responseData['Amount']) && $responseData['Amount'] !== '' && $responseData['Amount'] !== null
                ? (float) $responseData['Amount']
                : $requestedB2bAmount;
            $mpesaB2b->result_code = $responseData['ResultCode'] ?? null;
            $mpesaB2b->result_desc = $responseData['ResultDesc'] ?? null;
            $mpesaB2b->command_id = $responseData['CommandID'] ?? null;
            $mpesaB2b->initiator = config('mpesa.initiator', 'testapi');
            $mpesaB2b->security_credential = null;
            $mpesaB2b->remarks = $responseData['Remarks'] ?? null;
            $mpesaB2b->occasion = $responseData['Occasion'] ?? null;
            $mpesaB2b->status = 'pending';
            $mpesaB2b->raw_response = $responseData;
            $mpesaB2b->source_transaction_id = $transaction->transaction_id;
            $mpesaB2b->save();

            return [
                'success' => true,
                'message' => 'B2B payment initiated successfully.',
                'data' => $responseData,
            ];
        }
        // Log error response
        \Log::error('M-Pesa B2B Payment Error', [
            'response' => $response->json(),
            'transaction_id' => $transaction->transaction_id,
        ]);

        return [
            'success' => false,
            'message' => $response['errorMessage'] ?? 'B2B payment initiation failed.',
            'data' => $response->json(),
        ];
    }

    /**
     * Initiate B2C Payment (Business to Customer)
     */
    public function b2c(Transaction $transaction): array
    {
        $endpoint = config('mpesa.b2c_url', 'https://sandbox.safaricom.co.ke/mpesa/b2c/v1/paymentrequest');
        $token = $this->getAccessToken();
        if ($token === '') {
            return [
                'success' => false,
                'message' => 'M-Pesa authentication failed. Check consumer key/secret and try again.',
                'data' => null,
            ];
        }

        $partyB = preg_replace('/[\s+]/', '', (string) $transaction->receiver_mobile);
        if ($partyB === '' || ! preg_match('/^254\d{9}$/', $partyB)) {
            return [
                'success' => false,
                'message' => 'Recipient phone is missing or invalid for M-Pesa payout. It must be a Kenya number in 2547XXXXXXXX format.',
                'data' => null,
            ];
        }

        $amountKes = max(1, (int) round((float) $transaction->transaction_amount, 0));

        $payload = [
            'InitiatorName' => config('mpesa.initiator', 'testapi'),
            'SecurityCredential' => config('mpesa.security_credential', 'security'),
            'CommandID' => 'BusinessPayment',
            'Amount' => $amountKes,
            'PartyA' => config('mpesa.shortcode'),
            'PartyB' => $partyB,
            'Remarks' => $transaction->transaction_details ?? 'Escrow Payout',
            'QueueTimeOutURL' => $this->b2cTimeoutUrl(),
            'ResultURL' => $this->b2cResultUrl(),
            'Occasion' => $transaction->transaction_type ?? 'Escrow',
        ];
        // Log the payload for debugging
        \Log::info('M-Pesa B2C Payload', [
            'payload' => $payload,
            'transaction_id' => $transaction->transaction_id,
        ]);
        try {
            $response = $this->mpesaHttp()->timeout(60)->withToken($token)->acceptJson()->post($endpoint, $payload);
        } catch (\Throwable $e) {
            \Log::error('M-Pesa B2C HTTP exception', [
                'transaction_id' => $transaction->transaction_id,
                'message' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'message' => 'B2C request failed: '.$e->getMessage(),
                'data' => null,
            ];
        }

        $responseData = $response->json();
        if (! is_array($responseData)) {
            $responseData = [];
        }
        $responseCode = $responseData['ResponseCode'] ?? $responseData['responseCode'] ?? null;
        $accepted = $response->successful() && (string) $responseCode === '0';

        if ($accepted) {
            // fill the M-Pesa B2c table
            $mpesaB2c = new \App\Models\MpesaB2c;
            $mpesaB2c->originator_conversation_id = $responseData['OriginatorConversationID'] ?? null;
            $mpesaB2c->conversation_id = $responseData['ConversationID'] ?? null;
            $mpesaB2c->transaction_id = $responseData['TransactionID'] ?? null;
            $mpesaB2c->transaction_type = $responseData['TransactionType'] ?? null;
            $partyBRaw = $responseData['PartyB'] ?? null;
            $normalizedPartyB = ($partyBRaw !== null && $partyBRaw !== '')
                ? preg_replace('/[\s+]/', '', (string) $partyBRaw)
                : preg_replace('/[\s+]/', '', (string) $transaction->receiver_mobile);
            $mpesaB2c->receiver_mobile = $normalizedPartyB !== '' ? $normalizedPartyB : null;
            $mpesaB2c->party_b = $mpesaB2c->receiver_mobile;
            $requestedB2cAmount = (float) $transaction->transaction_amount;
            $mpesaB2c->amount = isset($responseData['Amount']) && $responseData['Amount'] !== '' && $responseData['Amount'] !== null
                ? (float) $responseData['Amount']
                : $requestedB2cAmount;
            $mpesaB2c->result_code = $responseData['ResultCode'] ?? null;
            $mpesaB2c->result_desc = $responseData['ResultDesc'] ?? null;
            $mpesaB2c->command_id = $responseData['CommandID'] ?? null;
            $mpesaB2c->initiator_name = $responseData['InitiatorName'] ?? null;
            $mpesaB2c->security_credential = $responseData['SecurityCredential'] ?? null;
            $mpesaB2c->party_a = $responseData['PartyA'] ?? null;
            $mpesaB2c->remarks = $responseData['Remarks'] ?? null;
            $mpesaB2c->occasion = $responseData['Occasion'] ?? null;
            $mpesaB2c->status = $responseData['Status'] ?? 'pending';
            $mpesaB2c->raw_response = $responseData;
            $mpesaB2c->source_transaction_id = $transaction->transaction_id;
            $mpesaB2c->save();

            return [
                'success' => true,
                'message' => 'B2C payment initiated successfully.',
                'data' => $responseData,
            ];
        }

        $detail = $responseData['errorMessage']
            ?? $responseData['error_description']
            ?? $responseData['ResponseDescription']
            ?? $responseData['responseDescription']
            ?? $responseData['ResultDesc']
            ?? null;
        if (! is_string($detail) || trim($detail) === '') {
            $detail = 'HTTP '.$response->status().'; ResponseCode='.var_export($responseCode, true);
        }

        \Log::warning('M-Pesa B2C initiation not accepted', [
            'transaction_id' => $transaction->transaction_id,
            'http_status' => $response->status(),
            'response_code' => $responseCode,
            'body' => $responseData,
            'raw' => $response->body(),
        ]);

        return [
            'success' => false,
            'message' => 'B2C payment initiation failed: '.$detail,
            'data' => $responseData,
        ];
    }

    /**
     * Query Transaction Status
     */
    public function transactionStatus(string $transactionId): array
    {
        $endpoint = config('mpesa.transaction_status_url', 'https://sandbox.safaricom.co.ke/mpesa/transactionstatus/v1/query');
        $token = $this->getAccessToken();
        $payload = [
            'Initiator' => config('mpesa.initiator', 'testapi'),
            'SecurityCredential' => config('mpesa.security_credential', 'security'),
            'CommandID' => 'TransactionStatusQuery',
            'TransactionID' => $transactionId,
            'PartyA' => config('mpesa.shortcode'),
            'IdentifierType' => '4',
            'ResultURL' => config('mpesa.status_result_url', url('/mpesa/status/result')),
            'QueueTimeOutURL' => config('mpesa.status_timeout_url', url('/mpesa/status/timeout')),
            'Remarks' => 'Transaction Status',
            'Occasion' => 'Escrow',
        ];
        // Log the payload for debugging
        \Log::info('M-Pesa Transaction Status Payload', [
            'payload' => $payload,
            'transaction_id' => $transactionId,
        ]);
        $response = $this->mpesaHttp()->withToken($token)->acceptJson()->post($endpoint, $payload);

        return $response->json();
    }

    /**
     * ResultDesc for STK *query* can say "The transaction is still under processing" with ResultCodes
     * outside {0,1,1037} — the API then maps to a hard "Failure" even though the payment is in flight.
     * Use a liberal text check (not only regex) so odd whitespace/encoding still match.
     */
    public static function stkQueryResultDescLooksInProgress(string $raw): bool
    {
        $d = trim(preg_replace('/\s+/u', ' ', (string) $raw));
        if ($d === '') {
            return false;
        }
        $lower = mb_strtolower($d, 'UTF-8');
        if (str_contains($lower, 'invalid') && str_contains($lower, 'credentials')) {
            return false;
        }
        if (str_contains($lower, 'cancel') && (str_contains($lower, 'user') || str_contains($lower, 'by'))) {
            return false;
        }
        if (str_contains($lower, 'insufficient') || str_contains($lower, 'rejected') || str_contains($lower, 'declined')) {
            return false;
        }
        if (str_contains($lower, 'under processing') || str_contains($lower, 'being processed') || str_contains($lower, 'still under processing')) {
            return true;
        }
        if (str_contains($lower, 'the transaction is still') && str_contains($lower, 'process')) {
            return true;
        }
        if (str_contains($lower, 'request') && str_contains($lower, 'being processed')) {
            return true;
        }

        return (bool) preg_match('/\b(in\s+progress|not\s+completed\s+yet)\b/iu', $d);
    }

    /**
     * M-Pesa STK Query often returns raw ResultDesc like "The service request is... still under processing"
     * when polled immediately after STK. Map those to a clearer, calmer line for the UI.
     */
    public static function friendlyStkQueryPendingMessage(string $raw): string
    {
        $d = trim($raw);
        if ($d === '') {
            return 'Awaiting M-Pesa confirmation (after you enter your PIN, this usually takes a few seconds).';
        }
        if (self::stkQueryResultDescLooksInProgress($d)) {
            return 'M-Pesa is still handling this request. If you have not yet approved, look for the payment prompt on your phone and enter your PIN.';
        }

        return $d;
    }

    /** Safaricom may send codes as int, string, or omit; empty/non-numeric → null. */
    protected static function stkQueryNumericCode(mixed $v): ?int
    {
        if ($v === null || $v === '') {
            return null;
        }
        if (is_bool($v)) {
            return null;
        }
        if (is_numeric($v)) {
            return (int) $v;
        }

        return null;
    }

    /**
     * @return array{0: int, 1: string}
     */
    protected static function parseStkPushQueryResultPayload(?array $body): array
    {
        if (! is_array($body) || $body === []) {
            return [1, ''];
        }
        $code = self::stkQueryNumericCode($body['ResultCode'] ?? $body['resultCode'] ?? null);
        $desc = $body['ResultDesc'] ?? $body['resultDesc'] ?? null;
        if (isset($body['Result']) && is_array($body['Result'])) {
            if ($code === null) {
                $code = self::stkQueryNumericCode($body['Result']['ResultCode'] ?? $body['Result']['resultCode'] ?? null);
            }
            if ($desc === null || $desc === '') {
                $desc = $body['Result']['ResultDesc'] ?? $body['Result']['resultDesc'] ?? $desc;
            }
        }
        if (isset($body['Body']) && is_array($body['Body'])) {
            if ($code === null) {
                $code = self::stkQueryNumericCode($body['Body']['ResultCode'] ?? $body['Body']['resultCode'] ?? null);
            }
            if ($desc === null || $desc === '') {
                $desc = $body['Body']['ResultDesc'] ?? $body['Body']['resultDesc'] ?? $desc;
            }
        }
        $bodyInner = is_array($body['Body'] ?? null) ? $body['Body'] : null;
        if ($bodyInner !== null && isset($bodyInner['stkCallback']) && is_array($bodyInner['stkCallback'])) {
            $stkCb = $bodyInner['stkCallback'];
            if ($code === null) {
                $code = self::stkQueryNumericCode($stkCb['ResultCode'] ?? $stkCb['resultCode'] ?? null);
            }
            if ($desc === null || $desc === '') {
                $desc = $stkCb['ResultDesc'] ?? $stkCb['resultDesc'] ?? $desc;
            }
        }
        if ($code === null) {
            $code = self::stkQueryNumericCode($body['ResponseCode'] ?? $body['responseCode'] ?? null);
        }
        if ($desc === null || $desc === '') {
            $desc = $body['ResponseDescription'] ?? $body['responseDescription'] ?? null;
        }

        return [
            (int) ($code ?? 1),
            (string) ($desc !== null && $desc !== '' ? $desc : 'Awaiting M-Pesa confirmation.'),
        ];
    }

    /**
     * Query STK push status by CheckoutRequestID.
     */
    public function stkPushQuery(string $checkoutRequestId): array
    {
        $timestamp = now()->format('YmdHis');
        $password = base64_encode(config('mpesa.shortcode').config('mpesa.passkey').$timestamp);
        $endpoint = $this->stkQueryEndpoint();
        $token = $this->getAccessToken();

        if ($token === '') {
            return [
                'success' => false,
                'status' => 'Pending',
                'message' => 'Unable to query M-Pesa status (auth failed).',
                'data' => null,
            ];
        }

        $payload = [
            'BusinessShortCode' => config('mpesa.shortcode'),
            'Password' => $password,
            'Timestamp' => $timestamp,
            'CheckoutRequestID' => $checkoutRequestId,
        ];

        try {
            $response = $this->mpesaHttp()->timeout(45)
                ->withToken($token)
                ->acceptJson()
                ->post($endpoint, $payload);
        } catch (\Throwable $e) {
            \Log::error('M-Pesa STK Query HTTP exception', [
                'checkout_request_id' => $checkoutRequestId,
                'message' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'status' => 'Pending',
                'message' => 'Still waiting for M-Pesa callback.',
                'data' => null,
            ];
        }

        $body = $response->json();
        if (! is_array($body)) {
            $body = [];
        }
        [$resultCode, $resultDesc] = self::parseStkPushQueryResultPayload($body);

        // Daraja query accepted and transaction completed successfully.
        if ($response->successful() && $resultCode === 0) {
            return [
                'success' => true,
                'status' => 'Success',
                'message' => $resultDesc,
                'data' => is_array($body) ? $body : null,
            ];
        }

        // Common non-final/pending outcomes while callback is delayed.
        if (in_array($resultCode, [1, 1037], true)) {
            return [
                'success' => false,
                'status' => 'Pending',
                'message' => self::friendlyStkQueryPendingMessage($resultDesc),
                'data' => is_array($body) ? $body : null,
            ];
        }

        // Non-standard ResultCode but plain English still says in-flight (do not map to a hard failure).
        if (self::stkQueryResultDescLooksInProgress($resultDesc)) {
            return [
                'success' => false,
                'status' => 'Pending',
                'message' => self::friendlyStkQueryPendingMessage($resultDesc),
                'data' => is_array($body) ? $body : null,
            ];
        }

        return [
            'success' => false,
            'status' => 'Failed',
            'message' => $resultDesc,
            'data' => is_array($body) ? $body : null,
        ];
    }

    /**
     * Query Account Balance
     */
    public function accountBalance(): array
    {
        $endpoint = config('mpesa.balance_url', 'https://sandbox.safaricom.co.ke/mpesa/accountbalance/v1/query');
        $token = $this->getAccessToken();
        $payload = [
            'Initiator' => config('mpesa.initiator', 'testapi'),
            'SecurityCredential' => config('mpesa.security_credential', 'security'),
            'CommandID' => 'AccountBalance',
            'PartyA' => config('mpesa.shortcode'),
            'IdentifierType' => '4',
            'Remarks' => 'Account Balance',
            'QueueTimeOutURL' => config('mpesa.balance_timeout_url', url('/mpesa/balance/timeout')),
            'ResultURL' => config('mpesa.balance_result_url', url('/mpesa/balance/result')),
        ];
        $response = $this->mpesaHttp()->withToken($token)->acceptJson()->post($endpoint, $payload);

        return $response->json();
    }

    /**
     * OAuth2 client_credentials against Daraja (sandbox or production per MPESA_OAUTH_URL).
     */
    public function getAccessToken(): string
    {
        $consumerKey = trim((string) config('mpesa.consumer_key'));
        $consumerSecret = trim((string) config('mpesa.consumer_secret'));

        if ($consumerKey === '' || $consumerSecret === '') {
            \Log::error('M-Pesa OAuth: MPESA_CONSUMER_KEY or MPESA_CONSUMER_SECRET is empty');

            return '';
        }

        $url = (string) config(
            'mpesa.oauth_url',
            'https://sandbox.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials'
        );

        try {
            $response = $this->mpesaHttp()->timeout(45)
                ->withBasicAuth($consumerKey, $consumerSecret)
                ->acceptJson()
                ->get($url);
        } catch (\Throwable $e) {
            \Log::error('M-Pesa OAuth request exception', [
                'message' => $e->getMessage(),
                'oauth_url' => $url,
            ]);

            return '';
        }

        if (! $response->successful()) {
            \Log::error('M-Pesa OAuth HTTP error', [
                'status' => $response->status(),
                'body' => $response->body(),
                'oauth_url' => $url,
            ]);

            return '';
        }

        $data = $response->json();
        $token = is_array($data) ? ($data['access_token'] ?? null) : null;

        if (! is_string($token) || $token === '') {
            \Log::error('M-Pesa OAuth: missing access_token in response', [
                'oauth_url' => $url,
                'parsed' => $data,
            ]);

            return '';
        }

        return $token;
    }

    /**
     * Submit an approved B2C row to Safaricom (updates the same database row on success).
     *
     * @return array{success: bool, message: string, data?: mixed}
     */
    public function submitB2cFromStoredRequest(MpesaB2c $record): array
    {
        if ($record->originator_conversation_id) {
            return ['success' => true, 'message' => 'Already submitted to Safaricom.'];
        }

        $partyB = preg_replace('/[\s+]/', '', (string) ($record->receiver_mobile ?: $record->party_b ?: ''));
        $amount = (float) ($record->amount ?? 0);
        if ($partyB === '' || $amount <= 0) {
            return ['success' => false, 'message' => 'M-Pesa B2C record is missing receiver phone or amount.'];
        }

        $endpoint = config('mpesa.b2c_url', 'https://sandbox.safaricom.co.ke/mpesa/b2c/v1/paymentrequest');
        $token = $this->getAccessToken();
        if ($token === '') {
            return ['success' => false, 'message' => 'Could not obtain M-Pesa access token.'];
        }

        $payload = [
            'InitiatorName' => config('mpesa.initiator', 'testapi'),
            'SecurityCredential' => config('mpesa.security_credential', 'security'),
            'CommandID' => 'BusinessPayment',
            'Amount' => $amount,
            'PartyA' => config('mpesa.shortcode'),
            'PartyB' => $partyB,
            'Remarks' => $record->remarks ?: 'Escrow Payout',
            'QueueTimeOutURL' => $this->b2cTimeoutUrl(),
            'ResultURL' => $this->b2cResultUrl(),
            'Occasion' => $record->occasion ?: 'Escrow',
        ];

        \Log::info('M-Pesa B2C (admin-approved record) payload', [
            'mpesa_b2c_id' => $record->id,
            'party_b_tail' => strlen($partyB) > 4 ? substr($partyB, -4) : $partyB,
        ]);

        $response = $this->mpesaHttp()->withToken($token)->acceptJson()->post($endpoint, $payload);

        if ($response->successful() && isset($response['ResponseCode']) && $response['ResponseCode'] == '0') {
            $responseData = $response->json();
            $record->fill([
                'originator_conversation_id' => $responseData['OriginatorConversationID'] ?? null,
                'conversation_id' => $responseData['ConversationID'] ?? null,
                'transaction_id' => $responseData['TransactionID'] ?? $record->transaction_id,
                'transaction_type' => $responseData['TransactionType'] ?? null,
                'receiver_mobile' => $responseData['PartyB'] ?? $record->receiver_mobile,
                'amount' => (isset($responseData['Amount']) && $responseData['Amount'] !== '' && $responseData['Amount'] !== null)
                    ? (float) $responseData['Amount']
                    : $record->amount,
                'result_code' => $responseData['ResultCode'] ?? null,
                'result_desc' => $responseData['ResultDesc'] ?? null,
                'command_id' => $responseData['CommandID'] ?? null,
                'initiator_name' => $responseData['InitiatorName'] ?? $record->initiator_name,
                'party_a' => $responseData['PartyA'] ?? $record->party_a,
                'party_b' => $responseData['PartyB'] ?? $record->party_b,
                'remarks' => $responseData['Remarks'] ?? $record->remarks,
                'occasion' => $responseData['Occasion'] ?? $record->occasion,
                'status' => $responseData['Status'] ?? 'processing',
                'raw_response' => $responseData,
            ]);
            $record->save();

            return [
                'success' => true,
                'message' => 'B2C payment submitted to Safaricom.',
                'data' => $responseData,
            ];
        }

        return [
            'success' => false,
            'message' => $response->json('errorMessage') ?? 'B2C payment initiation failed.',
            'data' => $response->json(),
        ];
    }

    /**
     * Submit an approved B2B row to Safaricom (updates the same database row on success).
     *
     * @return array{success: bool, message: string, data?: mixed}
     */
    public function submitB2bFromStoredRequest(MpesaB2b $record): array
    {
        if ($record->originator_conversation_id) {
            return ['success' => true, 'message' => 'Already submitted to Safaricom.'];
        }

        $partyB = trim((string) ($record->party_b ?? ''));
        $amount = (float) ($record->amount ?? 0);
        if ($partyB === '' || $amount <= 0) {
            return ['success' => false, 'message' => 'M-Pesa B2B record is missing paybill/till (Party B) or amount.'];
        }

        $endpoint = config('mpesa.b2b_url', 'https://sandbox.safaricom.co.ke/mpesa/b2b/v1/payment/request');
        $token = $this->getAccessToken();
        if ($token === '') {
            return ['success' => false, 'message' => 'Could not obtain M-Pesa access token.'];
        }

        $payload = [
            'Initiator' => config('mpesa.initiator'),
            'SecurityCredential' => config('mpesa.security_credential'),
            'CommandID' => 'BusinessPayBill',
            'SenderIdentifierType' => '4',
            'RecieverIdentifierType' => '4',
            'Amount' => round($amount),
            'PartyA' => config('mpesa.business_shortcode'),
            'PartyB' => $partyB,
            'Remarks' => $record->remarks ?: 'B2B transfer',
            'AccountReference' => $record->occasion ?: (string) $record->id,
            'Requester' => '254708374149',
            'QueueTimeOutURL' => config('mpesa.b2b_queue_timeout_url'),
            'ResultURL' => config('mpesa.b2b_results_url'),
            'Occasion' => $record->occasion ?: 'B2B',
        ];

        \Log::info('M-Pesa B2B (admin-approved record) payload', [
            'mpesa_b2b_id' => $record->id,
            'party_b' => $partyB,
        ]);

        $response = $this->mpesaHttp()->withToken($token)->acceptJson()->post($endpoint, $payload);

        if ($response->successful() && isset($response['ResponseCode']) && $response['ResponseCode'] == '0') {
            $responseData = $response->json();
            $record->fill([
                'originator_conversation_id' => $responseData['OriginatorConversationID'] ?? null,
                'conversation_id' => $responseData['ConversationID'] ?? null,
                'transaction_id' => $responseData['TransactionID'] ?? $record->transaction_id,
                'transaction_type' => $responseData['TransactionType'] ?? null,
                'party_a' => $responseData['PartyA'] ?? $record->party_a,
                'party_b' => $responseData['PartyB'] ?? $record->party_b,
                'amount' => (isset($responseData['Amount']) && $responseData['Amount'] !== '' && $responseData['Amount'] !== null)
                    ? (float) $responseData['Amount']
                    : $record->amount,
                'result_code' => $responseData['ResultCode'] ?? null,
                'result_desc' => $responseData['ResultDesc'] ?? null,
                'command_id' => $responseData['CommandID'] ?? null,
                'initiator' => config('mpesa.initiator', 'testapi'),
                'remarks' => $responseData['Remarks'] ?? $record->remarks,
                'occasion' => $responseData['Occasion'] ?? $record->occasion,
                'status' => 'processing',
                'raw_response' => $responseData,
            ]);
            $record->save();

            return [
                'success' => true,
                'message' => 'B2B transfer submitted to Safaricom.',
                'data' => $responseData,
            ];
        }

        return [
            'success' => false,
            'message' => $response->json('errorMessage') ?? 'B2B transfer initiation failed.',
            'data' => $response->json(),
        ];
    }

    /**
     * Simulate C2B Payment (Customer to Business)
     */
    public function c2b(array $data): array
    {
        // TODO: Implement C2B logic
        // Example endpoint and payload
        $endpoint = config('mpesa.c2b_url', 'https://sandbox.safaricom.co.ke/mpesa/c2b/v1/simulate');
        $token = $this->getAccessToken();
        $payload = [
            'ShortCode' => config('mpesa.shortcode'),
            'CommandID' => 'CustomerPayBillOnline',
            'Amount' => $data['amount'],
            'Msisdn' => $data['msisdn'],
            'BillRefNumber' => $data['reference'] ?? 'Escrow',
        ];
        $response = $this->mpesaHttp()->withToken($token)->acceptJson()->post($endpoint, $payload);

        return $response->json();
    }
}
