<?php

return [
    'app-name' => env('STATE_MONITOR_APP', ''),
    'use-local' => env('STATE_MONITOR_LOCAL_EMAIL', 'true'),
    'alert-email' => env('STATE_MONITOR_ALERT_EMAIL', ''),
    'use-bigquery' => env('STATE_MONITOR_BIGQUERY', 'true'),
    'bigquery-project' => env('STATE_MONITOR_GOOGLE_CLOUD_PROJECT', ''),
    'bigquery-path' => env('STATE_MONITOR_GOOGLE_APPLICATION_CREDENTIALS', ''),
    'log-queries' => env('STATE_MONITOR_LOG_QUERIES', 'false'),
    'use-middleware-web' => env('STATE_MONITOR_MIDDLEWARE_WEB', 'true'),
    'use-middleware-api' => env('STATE_MONITOR_MIDDLEWARE_API', 'true'),
];