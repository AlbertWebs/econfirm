<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use App\Models\Transaction;

class MpesaService
{
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

        $payload = [
            'BusinessShortCode' => config('mpesa.shortcode'),
            'Password' => $password,
            'Timestamp' => $timestamp,
            'TransactionType' => 'CustomerPayBillOnline',
            'Amount' => (int) preg_replace('/[\s+]/', '', $transaction->transaction_amount),
            'PartyA' => preg_replace('/[\s+]/', '', $transaction->sender_mobile),
            'PartyB' => config('mpesa.shortcode'),
            'PhoneNumber' => preg_replace('/[\s+]/', '', $transaction->sender_mobile),
            'CallBackURL' => config('mpesa.callback_url', url('/api/mpesa/callback')), // Ensure correct path
            'AccountReference' => $transaction->transaction_id,
            'TransactionDesc' => $transaction->transaction_details ?? 'Escrow Payment',
        ];

        $endpoint = config('mpesa.stk_url', 'https://sandbox.safaricom.co.ke/mpesa/stkpush/v1/processrequest');
        $token = $this->getAccessToken();

        $response = Http::withToken($token)->acceptJson()->post($endpoint, $payload);

        if ($response->successful() && isset($response['ResponseCode']) && $response['ResponseCode'] == '0') {
            $responseData = $response->json();

            // Save CheckoutRequestID and MerchantRequestID
            $transaction->checkout_request_id = $responseData['CheckoutRequestID'];
            $transaction->merchant_request_id = $responseData['MerchantRequestID'];
            $transaction->save();

            return [
                'success' => true,
                'message' => 'STK push initiated.',
                'data' => $responseData,
            ];
        }

        return [
            'success' => false,
            'message' => $response['errorMessage'] ?? 'STK push failed.',
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
        $response = Http::withToken($token)->acceptJson()->post($endpoint, $payload);
        return $response->json();
    }

    /**
     * Initiate B2C Payment (Business to Customer)
     */
    public function b2c(array $data): array
    {
        // TODO: Implement B2C logic
        $endpoint = config('mpesa.b2c_url', 'https://sandbox.safaricom.co.ke/mpesa/b2c/v1/paymentrequest');
        $token = $this->getAccessToken();
        $payload = [
            'InitiatorName' => config('mpesa.initiator', 'testapi'),
            'SecurityCredential' => config('mpesa.security_credential', 'security'),
            'CommandID' => 'BusinessPayment',
            'Amount' => $data['amount'],
            'PartyA' => config('mpesa.shortcode'),
            'PartyB' => $data['msisdn'],
            'Remarks' => $data['remarks'] ?? 'Escrow Payout',
            'QueueTimeOutURL' => config('mpesa.b2c_timeout_url', url('/mpesa/b2c/timeout')),
            'ResultURL' => config('mpesa.b2c_result_url', url('/mpesa/b2c/result')),
            'Occasion' => $data['occasion'] ?? 'Escrow',
        ];
        $response = Http::withToken($token)->acceptJson()->post($endpoint, $payload);
        return $response->json();
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
        $response = Http::withToken($token)->acceptJson()->post($endpoint, $payload);
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
        $response = Http::withToken($token)->acceptJson()->post($endpoint, $payload);
        return $response->json();
    }

    /**
     * Get M-Pesa Access Token (dummy implementation)
     * Replace with actual OAuth logic
     */
    public function getAccessToken(): string
    {
        $consumerKey = config('mpesa.consumer_key', env('MPESA_CONSUMER_KEY'));
        $consumerSecret = config('mpesa.consumer_secret', env('MPESA_CONSUMER_SECRET'));


        // M-Pesa base URL (sandbox or production)
        $url = 'https://sandbox.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials';

        // Generate credentials for HTTP Basic Authentication
        $credentials = base64_encode($consumerKey . ':' . $consumerSecret);

        // Initialize cURL
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Basic ' . $credentials,
            'Content-Type: application/json'
        ]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        // Execute request
        $response = curl_exec($ch);
        $err = curl_error($ch);
        curl_close($ch);

        if ($err) {
            echo 'cURL Error: ' . $err;
        } else {
            $result = json_decode($response);
            $accessToken = $result->access_token;

            return $accessToken;
        }
    }
}
