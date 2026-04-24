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
];
