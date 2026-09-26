<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Payment provider binding
    |--------------------------------------------------------------------------
    */
    'driver' => env('PAYOUT_PROVIDER', 'mock'),

    'mock' => [
        // null | succeeded | failed | timeout_after_success
        'forced_outcome' => env('PAYOUT_MOCK_OUTCOME'),
        'chance_failed' => (int) env('PAYOUT_MOCK_CHANCE_FAILED', 20),
        'chance_timeout' => (int) env('PAYOUT_MOCK_CHANCE_TIMEOUT', 20),
    ],
];
