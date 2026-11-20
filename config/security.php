<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Phone Number Encryption Key (SEC-03)
    |--------------------------------------------------------------------------
    |
    | 256-bit (32 bytes binary, represented as 64 hex characters) key
    | for standalone AES-256-GCM encryption of member phone numbers.
    |
    */
    'phone_key' => env('APP_PHONE_ENCRYPTION_KEY'),
];
