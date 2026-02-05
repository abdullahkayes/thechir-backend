<?php

/**
 * PayPal Configuration File
 * Generated for Laravel application
 */

return [
    'mode'    => env('PAYPAL_MODE', 'sandbox'), // Can be 'sandbox' or 'live'
    'sandbox' => [
        'client_id'     => env('PAYPAL_CLIENT_ID', ''),
        'client_secret' => env('PAYPAL_CLIENT_SECRET', ''),
        'app_id'        => '',
    ],
    'live' => [
        'client_id'     => env('PAYPAL_CLIENT_ID', ''),
        'client_secret' => env('PAYPAL_CLIENT_SECRET', ''),
        'app_id'        => '',
    ],
    'payment_action' => 'Sale', // Can be 'Sale', 'Authorization', 'Order'
    'currency'       => env('PAYPAL_CURRENCY', 'USD'),
    'notify_url'     => env('PAYPAL_NOTIFY_URL', ''), // Change this accordingly
    'locale'         => env('PAYPAL_LOCALE', 'en_US'), // force gateway language
    'validate_ssl'   => true,
];
