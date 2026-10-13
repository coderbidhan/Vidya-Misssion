<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Trial Configuration
    |--------------------------------------------------------------------------
    | Number of days for the free trial period.
    */
    'trial_days' => (int) env('TRIAL_DAYS', 3),

    /*
    |--------------------------------------------------------------------------
    | OTP Configuration
    |--------------------------------------------------------------------------
    */
    'otp_expiry_minutes' => (int) env('OTP_EXPIRY_MINUTES', 10),

    /*
    | Maximum OTP requests per email per hour (rate limiting)
    */
    'otp_rate_limit' => (int) env('OTP_RATE_LIMIT', 3),

    /*
    |--------------------------------------------------------------------------
    | Google Drive Configuration
    |--------------------------------------------------------------------------
    */
    'gdrive' => [
        'base_url' => env('GDRIVE_BASE_URL', 'https://drive.google.com/file/d/'),
        'client_id' => env('GOOGLE_DRIVE_CLIENT_ID'),
        'client_secret' => env('GOOGLE_DRIVE_CLIENT_SECRET'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Payment Gateway
    |--------------------------------------------------------------------------
    | Supported: "razorpay", "cashfree"
    */
    'payment_gateway' => env('PAYMENT_GATEWAY', 'razorpay'),

    'razorpay' => [
        'key_id' => env('RAZORPAY_KEY_ID'),
        'key_secret' => env('RAZORPAY_KEY_SECRET'),
        'webhook_secret' => env('RAZORPAY_WEBHOOK_SECRET'),
        'currency' => env('RAZORPAY_CURRENCY', 'INR'),
        'auto_capture' => (bool) env('RAZORPAY_AUTO_CAPTURE', true),
    ],

    'cashfree' => [
        'app_id' => env('CASHFREE_APP_ID'),
        'secret_key' => env('CASHFREE_SECRET_KEY'),
        'base_url' => env('CASHFREE_BASE_URL', 'https://sandbox.cashfree.com/pg'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Sanctum Token Expiry (in days)
    |--------------------------------------------------------------------------
    */
    'token_expiry' => [
        'student' => 30,
        'teacher' => 365,
        'admin' => 365,
    ],

];
