<?php

declare(strict_types=1);

namespace App\Main\Factories\Infra\Gateways;

use App\Infra\Gateways\Jwt;

use function Hyperf\Config\config;

class JwtFactory
{
    public static function make(): Jwt
    {
        return new Jwt(config('jwt'));
    }
}
