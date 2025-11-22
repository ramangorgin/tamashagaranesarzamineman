<?php
return [
    'webservice_url' => env('SMSIR_WEBSERVICE_URL', 'https://ws.sms.ir/'),
    'api_key' => env('SMSIR_API_KEY', ''),
    'secret_key' => env('SMSIR_SECRET_KEY', ''),
    'line_number' => env('SMSIR_LINE_NUMBER', ''),
    // Toggle database logging for sends (implement if needed later)
    'db_log' => false,
];
