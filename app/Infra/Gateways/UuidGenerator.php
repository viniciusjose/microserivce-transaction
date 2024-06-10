<?php

declare(strict_types=1);

namespace App\Infra\Gateways;

use App\Domain\Contracts\Gateways\UuidGeneratorInterface;
use Ramsey\Uuid\Uuid;

class UuidGenerator implements UuidGeneratorInterface
{
    public function generate(): string
    {
        return Uuid::uuid4()->toString();
    }
}
