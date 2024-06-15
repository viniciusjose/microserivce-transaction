<?php

namespace App\Domain\Contracts\Gateways;

interface JwtInterface
{
    public function encode(array $data): string;

    public function decode(string $token): array;
}