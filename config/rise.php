<?php

return [
    'app_name' => env('APP_NAME', 'RISE'),
    'tagline' => env('APP_TAGLINE', 'Reliable Inclusive Skill Education'),
    'razorpay' => [
        'key_id' => env('RAZORPAY_KEY_ID', 'rzp_live_SVKVZQDBHMqF8s'),
        'key_secret' => env('RAZORPAY_KEY_SECRET', 'Z80BrIDUvHq3Nv3cjGEV5wDj'),
    ],
    'min_recharge_amount' => (int) env('MIN_RECHARGE_AMOUNT', 100),
    'default_approval_fee' => (float) env('DEFAULT_APPROVAL_FEE', 2000),
    'currency' => env('CURRENCY', 'INR'),
    'currency_symbol' => env('CURRENCY_SYMBOL', '₹'),
    'program_approval_fees' => [
        22 => 200,
        25 => 200,
        26 => 200,
        29 => 200,
    ],
];

