<?php

return [
    /*
    | Public tariffs page + calculator defaults.
    |
    | Platform commission matches application logic (1% of escrow principal).
    | M-PESA tier tables are illustrative: replace with your partner / Safaricom
    | schedule and keep amounts in whole KES for display.
    */
    'commission_rate' => (float) env('TARIFFS_COMMISSION_RATE', 0.01),

    'mpesa' => [
        'b2c_tiers' => [
            ['min' => 1, 'max' => 49, 'fee' => 0],
            ['min' => 50, 'max' => 100, 'fee' => 0],
            ['min' => 101, 'max' => 500, 'fee' => 7],
            ['min' => 501, 'max' => 1000, 'fee' => 13],
            ['min' => 1001, 'max' => 1500, 'fee' => 23],
            ['min' => 1501, 'max' => 2500, 'fee' => 33],
            ['min' => 2501, 'max' => 3500, 'fee' => 53],
            ['min' => 3501, 'max' => 5000, 'fee' => 57],
            ['min' => 5001, 'max' => 7500, 'fee' => 78],
            ['min' => 7501, 'max' => 10000, 'fee' => 90],
            ['min' => 10001, 'max' => 15000, 'fee' => 100],
            ['min' => 15001, 'max' => 20000, 'fee' => 105],
            ['min' => 20001, 'max' => 25000, 'fee' => 123],
            ['min' => 25001, 'max' => 30000, 'fee' => 123],
            ['min' => 30001, 'max' => 35000, 'fee' => 123],
            ['min' => 35001, 'max' => 40000, 'fee' => 123],
            ['min' => 40001, 'max' => 45000, 'fee' => 123],
            ['min' => 45001, 'max' => 50000, 'fee' => 123],
            ['min' => 50001, 'max' => 70000, 'fee' => 123],
            ['min' => 70001, 'max' => 150000, 'fee' => 123],
        ],
        'b2b_tiers' => [
            ['min' => 1, 'max' => 100, 'fee' => 0],
            ['min' => 101, 'max' => 500, 'fee' => 5],
            ['min' => 501, 'max' => 1000, 'fee' => 10],
            ['min' => 1001, 'max' => 1500, 'fee' => 15],
            ['min' => 1501, 'max' => 2500, 'fee' => 20],
            ['min' => 2501, 'max' => 3500, 'fee' => 25],
            ['min' => 3501, 'max' => 5000, 'fee' => 34],
            ['min' => 5001, 'max' => 7500, 'fee' => 42],
            ['min' => 7501, 'max' => 10000, 'fee' => 48],
            ['min' => 10001, 'max' => 15000, 'fee' => 57],
            ['min' => 15001, 'max' => 20000, 'fee' => 62],
            ['min' => 20001, 'max' => 25000, 'fee' => 67],
            ['min' => 25001, 'max' => 30000, 'fee' => 72],
            ['min' => 30001, 'max' => 35000, 'fee' => 83],
            ['min' => 35001, 'max' => 40000, 'fee' => 99],
            ['min' => 40001, 'max' => 45000, 'fee' => 103],
            ['min' => 45001, 'max' => 50000, 'fee' => 108],
            ['min' => 50001, 'max' => 70000, 'fee' => 108],
            ['min' => 70001, 'max' => 150000, 'fee' => 108],
        ],
    ],
];
