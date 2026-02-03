<?php

namespace App\Services;

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
     * Normalize phone number to format accepted by Rebue Text API
     * Accepts: 254XXXXXXXXX, 07XXXXXXXX, 01XXXXXXXX, 7XXXXXXXX, 1XXXXXXXX
     *
     * @param string $phone Phone number
     * @return string Normalized phone number
     */
    protected function normalizePhone($phone)
    {
        // Remove any spaces, dashes, or plus signs
        $phone = preg_replace('/[\s+\-]/', '', $phone);

        // If it starts with 254 (Kenya country code), return as is
        if (preg_match('/^254\d{9}$/', $phone)) {
            return $phone;
        }

        // If it starts with 07 or 01 (10 digits), add 254 prefix
        if (preg_match('/^(07|01)\d{8}$/', $phone)) {
            return '254' . substr($phone, 1);
        }

        // If it starts with 7 or 1 (9 digits), add 254 prefix
        if (preg_match('/^(7|1)\d{8}$/', $phone)) {
            return '254' . $phone;
        }

        // Return as is if already in correct format or unknown format
        return $phone;
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
}
