<?php
return [
    'shortcode' => env('MPESA_SHORTCODE', '123456'),
    'business_shortcode' => env('MPESA_BUSINESS_SHORTCODE', '123456'),
    'passkey' => env('MPESA_PASSKEY', ''),
    'initiator' => env('MPESA_INITIATOR', ''),
    'security_credential' => env('MPESA_SECURITY_CREDENTIAL', ''),
    'callback_url' => env('MPESA_CALLBACK_URL'),
    'b2b_callback_url' => env('MPESA_B2B_CALLBACK_URL'), // or 'live'
    'b2b_results_url' => env('MPESA_B2B_RESULTS_URL'),
    'b2b_queue_timeout_url' => env('MPESA_B2B_QUEUE_TIMEOUT_URL'),
    'stk_url' => env('MPESA_STK_URL'),
    'c2b_url' => env('MPESA_C2B_URL'),
    'b2c_url' => env('MPESA_B2C_URL'),
    'b2b_url' => env('MPESA_B2B_URL'),
    'transaction_status_url' => env('MPESA_TRANSACTION_STATUS_URL'),
    'balance_url' => env('MPESA_BALANCE_URL'),
    'b2c_timeout_url' => env('MPESA_B2C_TIMEOUT_URL'),
    'b2c_result_url' => env('MPESA_B2C_RESULT_URL'),
    'status_result_url' => env('MPESA_STATUS_RESULT_URL'),
    'status_timeout_url' => env('MPESA_STATUS_TIMEOUT_URL'),
    'balance_timeout_url' => env('MPESA_BALANCE_TIMEOUT_URL'),
    'balance_result_url' => env('MPESA_BALANCE_RESULT_URL'),
    'access_token' => env('MPESA_ACCESS_TOKEN', ''),
    'consumer_key' => env('MPESA_CONSUMER_KEY', ''),
    'consumer_secret' => env('MPESA_CONSUMER_SECRET', ''),
    
];
