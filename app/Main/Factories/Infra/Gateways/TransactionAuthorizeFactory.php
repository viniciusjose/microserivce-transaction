<?php

declare(strict_types=1);

namespace App\Main\Factories\Infra\Gateways;

use App\Infra\Gateways\Jwt;

use App\Infra\Gateways\TransactionAuthorize;

use function Hyperf\Config\config;

class TransactionAuthorizeFactory
{
    public static function make(): TransactionAuthorize
    {
        return new TransactionAuthorize();
    }
}
