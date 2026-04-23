<?php

namespace App\Services;

use App\Models\Transaction;

class SmsService
{
    protected $apiUrl;
    protected $apiToken;
    protected $senderId;

    public function __construct()
    {
        // Get credentials from config/services.php
        $this->apiUrl = config('services.sms.api_url', 'https://rebuetext.com/api/v1/send-sms');
        $this->apiToken = config('services.sms.api_token');
        $this->senderId = config('services.sms.sender_id');
    }

    /**
     * Normalize a Kenya mobile number to 254XXXXXXXXX for SMS and lookups.
     * Accepts: 254XXXXXXXXX, 07XXXXXXXX, 01XXXXXXXX, 7XXXXXXXX, 1XXXXXXXX, +254…
     */
    public static function normalizeKenyaTo254(string $phone): string
    {
        $phone = preg_replace('/[\s+\-]/', '', $phone);

        if (preg_match('/^254\d{9}$/', $phone)) {
            return $phone;
        }

        if (preg_match('/^(07|01)\d{8}$/', $phone)) {
            return '254' . substr($phone, 1);
        }

        if (preg_match('/^(7|1)\d{8}$/', $phone)) {
            return '254' . $phone;
        }

        return $phone;
    }

    /**
     * @param string $phone Phone number
     * @return string Normalized phone number
     */
    protected function normalizePhone($phone)
    {
        return self::normalizeKenyaTo254($phone);
    }

    /**
     * Whether Rebue Text / cURL response indicates the SMS was accepted for delivery.
     */
    public static function resultIndicatesSuccess(array $result): bool
    {
        if (isset($result['status'])) {
            $s = $result['status'];

            return $s === true || $s === 1 || $s === '1' || $s === 'success';
        }

        return ! empty($result['data']['uniqueId']);
    }

    /**
     * Send an SMS message using Rebue Text API.
     *
     * @param string $to Recipient phone number (various formats accepted)
     * @param string $message Message content
     * @param string|null $correlator Optional unique identifier for tracking
     * @return array
     */
    public function send($to, $message, $correlator = null)
    {
        if (empty($this->apiToken)) {
            \Log::error('SMS API Token not configured');
            return ['status' => false, 'message' => 'SMS API Token not configured'];
        }

        if (empty($this->senderId)) {
            \Log::error('SMS Sender ID not configured');
            return ['status' => false, 'message' => 'SMS Sender ID not configured'];
        }

        // Normalize phone number
        $phone = $this->normalizePhone($to);

        // Prepare request body
        $payload = [
            'sender' => $this->senderId,
            'message' => $message,
            'phone' => $phone,
        ];

        // Add optional correlator if provided
        if ($correlator !== null) {
            $payload['correlator'] = $correlator;
        }

        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL => $this->apiUrl,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => json_encode($payload),
            CURLOPT_HTTPHEADER => [
                "Content-Type: application/json",
                "Accept: application/json",
                "Authorization: Bearer {$this->apiToken}",
            ],
        ]);

        $response = curl_exec($curl);
        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
            \Log::error('SMS cURL Error', ['error' => $err, 'phone' => $phone]);
            return ['status' => false, 'message' => "cURL Error: $err"];
        }

        $responseData = json_decode($response, true);

        // Handle response - Rebue Text returns an array
        if ($httpCode === 200 && is_array($responseData)) {
            // If it's an array with a single response, return the first element
            if (isset($responseData[0])) {
                $result = $responseData[0];
                // Log successful send
                if (isset($result['status']) && $result['status']) {
                    \Log::info('SMS sent successfully', [
                        'phone' => $phone,
                        'uniqueId' => $result['data']['uniqueId'] ?? null,
                    ]);
                } else {
                    \Log::warning('SMS send failed', [
                        'phone' => $phone,
                        'message' => $result['message'] ?? 'Unknown error',
                    ]);
                }
                return $result;
            }
            return $responseData;
        }

        // Handle error responses
        \Log::error('SMS API Error', [
            'http_code' => $httpCode,
            'response' => $responseData,
            'phone' => $phone,
        ]);

        return [
            'status' => false,
            'message' => $responseData['message'] ?? 'Failed to send SMS',
            'http_code' => $httpCode,
        ];
    }

    /**
     * Send SMS to multiple recipients (comma-separated phone numbers).
     *
     * @param string $to Comma-separated phone numbers
     * @param string $message Message content
     * @param string|null $correlator Optional unique identifier for tracking
     * @return array
     */
    public function sendBulk($to, $message, $correlator = null)
    {
        // Rebue Text API supports comma-separated phone numbers
        return $this->send($to, $message, $correlator);
    }

    /**
     * SMS when escrow STK is initiated.
     * Intentionally disabled: both parties are notified only after escrow is funded.
     */
    public function notifyEscrowStkInitiated(Transaction $transaction): void
    {
        \Log::info('Escrow STK initiation SMS suppressed by policy', [
            'transaction_id' => $transaction->transaction_id,
        ]);
    }

    /**
     * Public HTTPS URL for the transaction portal (SMS, link previews).
     */
    public static function absoluteTransactionPortalUrl(string $transactionId): string
    {
        return route('transaction.index', ['id' => $transactionId], true);
    }

    /**
     * SMS after M-Pesa confirms escrow is funded — includes link to view / approve on the portal.
     */
    public function notifyEscrowFunded(Transaction $transaction): void
    {
        if (empty($this->apiToken) || empty($this->senderId)) {
            \Log::warning('Escrow funded SMS skipped: SMS not configured in .env');

            return;
        }

        $url = self::absoluteTransactionPortalUrl($transaction->transaction_id);
        $id = $transaction->transaction_id;
        $amt = number_format((float) $transaction->transaction_amount, 2);

        $senderMsg = "eConfirm: Escrow {$id} is funded. Review & approve next steps: {$url}";

        if (($transaction->payment_method ?? 'mpesa') === 'mpesa') {
            $receiverMsg = "eConfirm: Escrow {$id} — KES {$amt} secured. View details & approve: {$url}";
        } else {
            $pb = $transaction->paybill_till_number ?? '';
            $receiverMsg = "eConfirm: Escrow {$id} — payout via Paybill/Till {$pb}. Details: {$url}";
        }

        $this->send($transaction->sender_mobile, $senderMsg, $id.'-funded-sender');
        $this->send($transaction->receiver_mobile, $receiverMsg, $id.'-funded-recv');
    }

    /**
     * Sent only after Daraja B2B or B2C call succeeds (payout initiated).
     *
     * @param  bool  $isB2bToPaybill  true when payment_method is paybill (B2B to Paybill/Till); false for M-Pesa B2C to phone.
     */
    public function notifyPartiesAfterApprovedPayout(Transaction $transaction, bool $isB2bToPaybill): void
    {
        if (empty($this->apiToken) || empty($this->senderId)) {
            \Log::warning('Post-approval payout SMS skipped: SMS not configured');

            return;
        }

        $ref = $transaction->transaction_id;
        $amt = number_format((float) $transaction->transaction_amount, 2);

        $senderMsg = "eConfirm: You approved escrow {$ref}. We've started sending KES {$amt} to the recipient.";

        if ($isB2bToPaybill) {
            $pb = $transaction->paybill_till_number ?? '';
            $receiverMsg = "eConfirm: Your payment has been approved! KES {$amt} is on its way to Paybill/Till {$pb} — coming your way in a bit.";
        } else {
            $receiverMsg = "eConfirm: Your payment has been approved! Your money is headed to your M-Pesa — transaction coming your way in a bit.";
        }

        $this->send($transaction->sender_mobile, $senderMsg, $ref.'-payout-sender');
        $this->send($transaction->receiver_mobile, $receiverMsg, $ref.'-payout-recv');
    }
}
