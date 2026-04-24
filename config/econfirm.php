<?php

return [
    /*
     | "Trusted by" counter: on `start_date` the displayed value is `base`. Each
     | subsequent calendar day adds a deterministic 1–15 (same for everyone that day).
     */
    'trusted_clients' => [
        'base' => (int) env('TRUSTED_CLIENTS_BASE', 225),
        'start_date' => env('TRUSTED_CLIENTS_START_DATE', '2026-04-24'),
    ],

    /*
     | Public REST API (Escrow v1). If unset, derived from app.url + /api/v1.
     | Override when the API is on a different host (e.g. https://api.example.com/v1).
     */
    'api' => [
        'v1_url' => env('ECONFIRM_API_V1_URL'),
    ],
];
