<?php

return [
    'host' => env('MONERO_RPC_HOST', '127.0.0.1'),
    'port' => env('MONERO_RPC_PORT', 18082),
    'ssl' => env('MONERO_RPC_SSL', false),
    'vendor_payment_required_amount' => env('MONERO_VENDOR_PAYMENT_REQUIRED_AMOUNT', 0.4),
    'vendor_payment_minimum_amount' => env('MONERO_VENDOR_PAYMENT_MINIMUM_AMOUNT', 0.04),
    'vendor_payment_refund_percentage' => env('MONERO_VENDOR_PAYMENT_REFUND_PERCENTAGE', 80), /*This value must never be set to zero.*/
    'address_expiration_time' => env('MONERO_ADDRESS_EXPIRATION_TIME', 1440),
];
