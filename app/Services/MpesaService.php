<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use App\Models\Transaction;
use App\Models\MpesaStkPush;

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
            'CallBackURL' => config('mpesa.callback_url'), // Ensure correct path
            'AccountReference' => $transaction->transaction_id,
            'TransactionDesc' => $transaction->transaction_details ?? 'Escrow Payment',
        ];
        //Log Payload
        \Log::info('M-Pesa STK Push Payload', [
            'payload' => $payload,
            'transaction_id' => $transaction->transaction_id,
        ]);

        $endpoint = config('mpesa.stk_url', 'https://sandbox.safaricom.co.ke/mpesa/stkpush/v1/processrequest');
        $token = $this->getAccessToken();

        $response = Http::withToken($token)->acceptJson()->post($endpoint, $payload);

        if ($response->successful() && isset($response['ResponseCode']) && $response['ResponseCode'] == '0') {
            $responseData = $response->json();

            // Save CheckoutRequestID and MerchantRequestID
            $MpesaStkPush = new MpesaStkPush();
            $MpesaStkPush->phone = $transaction->sender_mobile;
            $MpesaStkPush->amount = $transaction->transaction_amount;
            $MpesaStkPush->reference = $transaction->transaction_id;
            $MpesaStkPush->description = $transaction->transaction_details ?? 'Escrow Payment';
            $MpesaStkPush->status = 'Pending';
            $MpesaStkPush->checkout_request_id = $responseData['CheckoutRequestID'];
            $MpesaStkPush->merchant_request_id = $responseData['MerchantRequestID'];
            $MpesaStkPush->save();

            return [
                'success' => true,
                'message' => 'STK push initiated.',
                'data' => $responseData,
            ];
        }

        //log responseData
        \Log::error('M-Pesa STK Push Error', [
            'response' => $response->json(),
            'transaction_id' => $transaction->transaction_id,
        ]);
        return [
            'success' => false,
            'message' => $response['errorMessage'] ?? 'STK push failed.',
            'data' => $response->json(),
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
        $response = Http::withToken($token)->acceptJson()->post($endpoint, $payload);
       
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
        $response = Http::withToken($token)->acceptJson()->post($endpoint, $payload);
         
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
  
}
