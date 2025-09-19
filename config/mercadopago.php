<?php

return [
    /*
    |--------------------------------------------------------------------------
    | MercadoPago Configuration
    |--------------------------------------------------------------------------
    |
    | This file is for storing the configuration for MercadoPago integration.
    |
    */

    'access_token' => env('MERCADOPAGO_ACCESS_TOKEN'),
    'public_key' => env('MERCADOPAGO_PUBLIC_KEY'),
    'client_secret' => env('MERCADOPAGO_CLIENT_SECRET'),
    'client_id' => env('MERCADOPAGO_CLIENT_ID'),

    /*
    |--------------------------------------------------------------------------
    | Environment
    |--------------------------------------------------------------------------
    |
    | Set to 'sandbox' for testing or 'production' for live transactions
    |
    */
    'environment' => env('APP_ENV') === 'production' ? 'production' : 'sandbox',
];