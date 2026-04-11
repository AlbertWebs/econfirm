<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use App\Models\Transaction;
use App\Models\MpesaStkPush;

class MpesaService
{
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
     * @param Transaction $transaction
     * @return array
     */
  public function stkPush(Transaction $transaction): array
    {
        $timestamp = now()->format('YmdHis');
        $password = base64_encode(config('mpesa.shortcode') . config('mpesa.passkey') . $timestamp);

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

        //Log Payload
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
            $MpesaStkPush = new MpesaStkPush();
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
     *
     * @param Transaction $transaction
     * @return array
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
            'Requester'=> '254708374149',
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
            $mpesaB2b = new \App\Models\MpesaB2b();
            $mpesaB2b->originator_conversation_id = $responseData['OriginatorConversationID'] ?? null;
            $mpesaB2b->conversation_id = $responseData['ConversationID'] ?? null;
            $mpesaB2b->transaction_id = $responseData['TransactionID'] ?? null;
            $mpesaB2b->transaction_type = $responseData['TransactionType'] ?? null;
            $mpesaB2b->party_a = $responseData['PartyA'] ?? null;
            $mpesaB2b->party_b = $responseData['PartyB'] ?? null;
            $mpesaB2b->amount = $responseData['Amount'] ?? null;
            $mpesaB2b->result_code = $responseData['ResultCode'] ?? null;
            $mpesaB2b->result_desc = $responseData['ResultDesc'] ?? null;
            $mpesaB2b->command_id = $responseData['CommandID'] ?? null;
            $mpesaB2b->initiator = config('mpesa.initiator', 'testapi');
            $mpesaB2b->security_credential = null;
            $mpesaB2b->remarks = $responseData['Remarks'] ?? null;
            $mpesaB2b->occasion = $responseData['Occasion'] ?? null;
            $mpesaB2b->status = 'pending';
            $mpesaB2b->raw_response = $responseData;
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
        // TODO: Implement B2C logic
        $endpoint = config('mpesa.b2c_url', 'https://sandbox.safaricom.co.ke/mpesa/b2c/v1/paymentrequest');
        $token = $this->getAccessToken();
        $payload = [
            'InitiatorName' => config('mpesa.initiator', 'testapi'),
            'SecurityCredential' => config('mpesa.security_credential', 'security'),
            'CommandID' => 'BusinessPayment',
            'Amount' => $transaction->transaction_amount,
            'PartyA' => config('mpesa.shortcode'),
            'PartyB' => preg_replace('/[\s+]/', '', $transaction->receiver_mobile),
            'Remarks' => $transaction->transaction_details ?? 'Escrow Payout',
            'QueueTimeOutURL' => config('mpesa.b2c_timeout_url', url('/mpesa/b2c/timeout')),
            'ResultURL' => config('mpesa.b2c_result_url', url('/mpesa/b2c/result')),
            'Occasion' => $transaction->transaction_type ?? 'Escrow',
        ];
        // Log the payload for debugging
        \Log::info('M-Pesa B2C Payload', [
            'payload' => $payload,
            'transaction_id' => $transaction->transaction_id,
        ]);
        $response = $this->mpesaHttp()->withToken($token)->acceptJson()->post($endpoint, $payload);

        if ($response->successful() && isset($response['ResponseCode']) && $response['ResponseCode'] == '0') {
           $responseData = $response->json();
          
           //fill the M-Pesa B2c table
              $mpesaB2c = new \App\Models\MpesaB2c();
              $mpesaB2c->originator_conversation_id = $responseData['OriginatorConversationID'] ?? null;
              $mpesaB2c->conversation_id = $responseData['ConversationID'] ?? null;
              $mpesaB2c->transaction_id = $responseData['TransactionID'] ?? null;
              $mpesaB2c->transaction_type = $responseData['TransactionType'] ?? null;
              $mpesaB2c->receiver_mobile = $responseData['PartyB'] ?? null;
              $mpesaB2c->amount = $responseData['Amount'] ?? null;
              $mpesaB2c->result_code = $responseData['ResultCode'] ?? null;
              $mpesaB2c->result_desc = $responseData['ResultDesc'] ?? null;
              $mpesaB2c->command_id = $responseData['CommandID'] ?? null;
              $mpesaB2c->initiator_name = $responseData['InitiatorName'] ?? null;
              $mpesaB2c->security_credential = $responseData['SecurityCredential'] ?? null;
              $mpesaB2c->party_a = $responseData['PartyA'] ?? null;
              $mpesaB2c->party_b = $responseData['PartyB'] ?? null;
              $mpesaB2c->remarks = $responseData['Remarks'] ?? null;
              $mpesaB2c->occasion = $responseData['Occasion'] ?? null;
              $mpesaB2c->status = $responseData['Status'] ?? 'pending';
              $mpesaB2c->raw_response = $responseData;
              $mpesaB2c->save();
            return [
                'success' => true,
                'message' => 'B2C payment initiated successfully.',
                'data' => $responseData,
            ];
        }
        return [
            'success' => false,
            'message' => 'B2C payment initiation failed.',
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
