<?php

declare(strict_types=1);

use function Hyperf\Support\env;

return [
    'iss' => env('JWT_ISSUER', 'you_issuer'),
    'aud' => env('JWT_AUDIENCE', 'you_audience'),
    'secret' => env('JWT_SECRET', 'you_secret'),
    'alg' => env('JWT_ALG', 'HS256'),
    'ttl' => env('JWT_TTL', 3600),
];