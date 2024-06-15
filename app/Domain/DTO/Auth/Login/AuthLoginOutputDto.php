<?php

namespace App\Domain\DTO\Auth\Login;

readonly class AuthLoginOutputDto
{
    public function __construct(
        public string $token,
    ) {
    }
}