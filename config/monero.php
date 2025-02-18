<?php

return [
    'host' => env('MONERO_RPC_HOST', '127.0.0.1'),
    'port' => env('MONERO_RPC_PORT', 18082),
    'ssl' => env('MONERO_RPC_SSL', false),
    'vendor_payment_required_amount' => env('MONERO_VENDOR_PAYMENT_REQUIRED_AMOUNT', 0.4),
    'vendor_payment_minimum_amount' => env('MONERO_VENDOR_PAYMENT_MINIMUM_AMOUNT', 0.04),
    'vendor_payment_refund_percentage' => env('MONERO_VENDOR_PAYMENT_REFUND_PERCENTAGE', 80), /*This value must never be set to zero.*/
    'address_expiration_time' => env('MONERO_ADDRESS_EXPIRATION_TIME', 1440),

    /*
    |--------------------------------------------------------------------------
    | Advertisement Slot Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for advertisement slots and their pricing.
    | Base price is 0.10 XMR per day for slot 1, with multipliers for other slots.
    |
    */
    'advertisement_base_price' => env('MONERO_ADVERTISEMENT_BASE_PRICE', 0.10),
    'advertisement_slot_multipliers' => [
        1 => 1.00,    // Slot 1: 0.10 XMR/day
        2 => 0.85,    // Slot 2: 0.085 XMR/day
        3 => 0.70,    // Slot 3: 0.07 XMR/day
        4 => 0.55,    // Slot 4: 0.055 XMR/day
        5 => 0.40,    // Slot 5: 0.04 XMR/day
        6 => 0.30,    // Slot 6: 0.03 XMR/day
        7 => 0.20,    // Slot 7: 0.02 XMR/day
        8 => 0.10,    // Slot 8: 0.01 XMR/day
    ],
    'advertisement_max_duration' => env('MONERO_ADVERTISEMENT_MAX_DURATION', 30), // Maximum days for an advertisement
    'advertisement_min_duration' => env('MONERO_ADVERTISEMENT_MIN_DURATION', 1),  // Minimum days for an advertisement
];
