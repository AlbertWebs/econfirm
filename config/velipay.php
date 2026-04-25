<?php

return [
    /*
    |--------------------------------------------------------------------------
    | VeliPay API Configuration
    |--------------------------------------------------------------------------
    */
    'base_url' => env('VELIPAY_BASE_URL', 'https://api.pay.velinexlabs.com'),
    'api_key_id' => env('VELIPAY_API_KEY_ID', ''),
    'api_key_secret' => env('VELIPAY_API_KEY_SECRET', ''),
    'webhook_secret' => env('VELIPAY_WEBHOOK_SECRET', ''),

    /*
    |--------------------------------------------------------------------------
    | Escrow checkout behavior
    |--------------------------------------------------------------------------
    */
    'stk_max_uncompleted_per_ip' => (int) env('VELIPAY_MAX_UNCOMPLETED_PER_IP', 3),
];
