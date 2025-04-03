<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Monero RPC Connection Settings
    |--------------------------------------------------------------------------
    |
    | These settings define the connection parameters for the Monero wallet RPC.
    | The wallet RPC daemon must be running and accessible at these coordinates.
    |
    */
    'host' => env('MONERO_RPC_HOST', '127.0.0.1'),
    'port' => env('MONERO_RPC_PORT', 18082),
    'ssl' => env('MONERO_RPC_SSL', false),

    /*
    |--------------------------------------------------------------------------
    | Vendor Payment Settings
    |--------------------------------------------------------------------------
    |
    | Configuration for vendor registration payments:
    | - required_amount: Total amount needed to become a vendor (in XMR)
    | - minimum_amount: Minimum transaction size accepted (in XMR)
    | - refund_percentage: Percentage refunded if application is rejected
    | - address_expiration_time: How long (in minutes) a payment address remains valid
    |
    */
    'vendor_payment_required_amount' => env('MONERO_VENDOR_PAYMENT_REQUIRED_AMOUNT', 0.4),
    'vendor_payment_minimum_amount' => env('MONERO_VENDOR_PAYMENT_MINIMUM_AMOUNT', 0.04),
    'vendor_payment_refund_percentage' => env('MONERO_VENDOR_PAYMENT_REFUND_PERCENTAGE', 80), /* This value must never be set to zero */
    'address_expiration_time' => env('MONERO_ADDRESS_EXPIRATION_TIME', 1440), /* 24 hours */

    /*
    |--------------------------------------------------------------------------
    | Advertisement System Configuration
    |--------------------------------------------------------------------------
    |
    | Settings for the product advertisement payments:
    | - base_price: Base price for slot 1 in XMR per day
    | - slot_multipliers: Price multipliers for each slot (1-8)
    | - max_duration: Maximum advertisement duration in days
    | - min_duration: Minimum advertisement duration in days
    |
    | Slot pricing is calculated as: base_price * slot_multiplier * number_of_days
    | Example: Slot 1 for 1 day = 0.10 XMR * 1.00 = 0.10 XMR
    |          Slot 2 for 1 day = 0.10 XMR * 0.85 = 0.085 XMR
    |
    */
    'advertisement_base_price' => env('MONERO_ADVERTISEMENT_BASE_PRICE', 0.10),
    'advertisement_slot_multipliers' => [
        1 => 1.00,    /* 0.10 XMR/day */
        2 => 0.85,    /* 0.085 XMR/day */
        3 => 0.70,    /* 0.07 XMR/day */
        4 => 0.55,    /* 0.055 XMR/day */
        5 => 0.40,    /* 0.04 XMR/day */
        6 => 0.30,    /* 0.03 XMR/day */
        7 => 0.20,    /* 0.02 XMR/day */
        8 => 0.10,    /* 0.01 XMR/day */
    ],
    'advertisement_max_duration' => env('MONERO_ADVERTISEMENT_MAX_DURATION', 30),
    'advertisement_min_duration' => env('MONERO_ADVERTISEMENT_MIN_DURATION', 1),

    /*
    |--------------------------------------------------------------------------
    | Advertisement Payment Thresholds
    |--------------------------------------------------------------------------
    |
    | Minimum payment percentage threshold for advertisements.
    | Payments below this percentage of the total required amount will be ignored.
    | Default is 10% (0.10)
    |
    */
    'advertisement_minimum_payment_percentage' => env('MONERO_ADVERTISEMENT_MINIMUM_PAYMENT_PERCENTAGE', 0.10),

    /*
    |--------------------------------------------------------------------------
    | Cancelled Order Refund Settings
    |--------------------------------------------------------------------------
    |
    | Configuration for refunds on cancelled orders:
    | - commission_percentage: Percentage of the total that is kept as commission when an order is cancelled
    |   For example, if set to 1, a user who cancels an order will receive 99% of their payment back.
    |
    */
    'cancelled_order_commission_percentage' => env('MONERO_CANCELLED_ORDER_COMMISSION_PERCENTAGE', 1.0),
];
