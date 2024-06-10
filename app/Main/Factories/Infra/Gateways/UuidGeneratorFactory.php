<?php

declare(strict_types=1);

namespace App\Main\Factories\Infra\Gateways;

use App\Infra\Gateways\UuidGenerator;

class UuidGeneratorFactory
{
    public static function make(): UuidGenerator
    {
        return new UuidGenerator();
    }
}
