<?php

return [
    // Both the ZAP API and scan target must remain on this machine.
    'api_url' => env('ZAP_API_URL', 'http://127.0.0.1:8080'),
    'api_key' => env('ZAP_API_KEY', ''),
    'target_url' => env('ZAP_TARGET_URL', 'http://127.0.0.1:8000'),
    'spider_timeout_seconds' => (int) env('ZAP_SPIDER_TIMEOUT_SECONDS', 30),
    'passive_scan_timeout_seconds' => (int) env('ZAP_PASSIVE_SCAN_TIMEOUT_SECONDS', 30),
];