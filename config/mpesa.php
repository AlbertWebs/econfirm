<?php

return [
    'shortcode' => env('MPESA_SHORTCODE', '123456'),
    'business_shortcode' => env('MPESA_BUSINESS_SHORTCODE', '123456'),
    'passkey' => env('MPESA_PASSKEY', ''),
    'initiator' => env('MPESA_INITIATOR', ''),
    'security_credential' => env('MPESA_SECURITY_CREDENTIAL', ''),
    'oauth_url' => env('MPESA_OAUTH_URL', 'https://sandbox.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials'),
    'callback_url' => env('MPESA_CALLBACK_URL'),
    'b2b_callback_url' => env('MPESA_B2B_CALLBACK_URL'), // or 'live'
    'b2b_results_url' => env('MPESA_B2B_RESULTS_URL'),
    'b2b_queue_timeout_url' => env('MPESA_B2B_QUEUE_TIMEOUT_URL'),
    'stk_url' => env('MPESA_STK_URL'),
    'c2b_url' => env('MPESA_C2B_URL'),
    'b2c_url' => env('MPESA_B2C_URL'),
    'b2b_url' => env('MPESA_B2B_URL'),
    'reversal_url' => env('MPESA_REVERSAL_URL', 'https://sandbox.safaricom.co.ke/mpesa/reversal/v1/request'),
    'reversal_result_url' => env('MPESA_REVERSAL_RESULT_URL'),
    'reversal_queue_timeout_url' => env('MPESA_REVERSAL_QUEUE_TIMEOUT_URL'),
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

    /*
     * SSL for Guzzle (OAuth + STK + all Daraja calls). Error 60 on Windows = missing CA bundle.
     * Fix properly: set openssl.cafile and curl.cainfo in php.ini to cacert.pem, or set MPESA_CA_BUNDLE.
     * Local-only workaround: MPESA_HTTP_VERIFY_SSL=false (never use false in production).
     */
    'verify_ssl' => filter_var(env('MPESA_HTTP_VERIFY_SSL', true), FILTER_VALIDATE_BOOLEAN),
    'ca_bundle' => env('MPESA_CA_BUNDLE'),

    /*
     * Block new STK pushes when this IP already has N rows in mpesa_stk_pushes with status Pending.
     * Set to 0 to disable. Requires initiator_ip column (see migration).
     */
    'stk_max_uncompleted_per_ip' => (int) env('STK_MAX_UNCOMPLETED_PER_IP', 3),

    /**
     * When true, POST /api/v1/payments/c2b may call Daraja C2B simulate (typically sandbox only).
     * Disabled by default in production unless explicitly enabled.
     */
    'allow_partner_c2b_simulate' => filter_var(env('MPESA_ALLOW_PARTNER_C2B_SIMULATE', false), FILTER_VALIDATE_BOOLEAN),
];
