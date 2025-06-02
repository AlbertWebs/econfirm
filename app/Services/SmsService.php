<?php

namespace App\Services;

class SmsService
{
    protected $apiUrl;
    protected $apiKey;
    protected $userId;
    protected $password;
    protected $senderId;

    public function __construct()
    {
        // Get credentials from config/services.php
        $this->apiUrl = config('services.sms.api_url', 'https://smsportal.hostpinnacle.co.ke/SMSApi/send');
        $this->apiKey = config('services.sms.api_key');
        $this->userId = config('services.sms.user_id');
        $this->password = config('services.sms.password');
        $this->senderId = config('services.sms.sender_id'); // Default sender ID
    }

    /**
     * Send an SMS message.
     *
     * @param string $to Recipient phone number (in international format)
     * @param string $message Message content
     * @return array
     */
    public function send($to, $message)
    {
        $senderId = $this->senderId;

        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL => $this->apiUrl,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => http_build_query([
                'userid' => $this->userId,
                'password' => $this->password,
                'mobile' => $to,
                'msg' => $message,
                'senderid' => $senderId,
                'msgType' => 'text',
                'duplicatecheck' => 'true',
                'output' => 'json',
                'sendMethod' => 'quick',
            ]),
            CURLOPT_HTTPHEADER => [
                "apikey: {$this->apiKey}",
                "cache-control: no-cache",
                "content-type: application/x-www-form-urlencoded",
            ],
        ]);

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
            return ['status' => 'error', 'message' => "cURL Error #: $err"];
        }

        return json_decode($response, true);
    }
}
