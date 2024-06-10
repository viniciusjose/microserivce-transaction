<?php

namespace App\Domain\Contracts\Gateways;

interface UuidGeneratorInterface
{
    public function generate(): string;
}
