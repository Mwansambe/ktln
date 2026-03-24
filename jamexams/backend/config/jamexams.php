<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Activation Settings
    |--------------------------------------------------------------------------
    */
    'activation_duration_days' => env('ACTIVATION_DURATION_DAYS', 30),

    'expiry_message' => env(
        'ACTIVATION_EXPIRY_MESSAGE',
        "Lipa 1,000 Voda 0756527718 January\nTuma Ujumbe Malipo WhatsApp"
    ),

    /*
    |--------------------------------------------------------------------------
    | File Upload Settings
    |--------------------------------------------------------------------------
    */
    'max_exam_size'    => env('MAX_EXAM_SIZE', 51200),  // 50MB in KB
    'exam_storage_path' => env('EXAM_STORAGE_PATH', 'exams'),

    /*
    |--------------------------------------------------------------------------
    | API Settings
    |--------------------------------------------------------------------------
    */
    'api_rate_limit' => env('API_RATE_LIMIT', 60),
];
