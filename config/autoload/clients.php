<?php

use function Hyperf\Support\env;

return [
    'authorization' => [
        'base_uri' => env('AUTHORIZATION_BASE_URI'),
    ],
    'notification' => [
        'base_uri' => env('NOTIFICATION_BASE_URI'),
    ],
];