<?php

return [
    // API key/secret from Sepay; set SEPAY_API_KEY in .env
    'api_key' => env('SEPAY_API_KEY', ''),

    // Optional static token header for webhook verification
    'webhook_token' => env('SEPAY_WEBHOOK_TOKEN', ''),

    // Prefix/pattern included in transfer content to match orders, e.g. ORD or EH
    'order_prefix' => env('SEPAY_ORDER_PREFIX', 'ORD'),
];
