<?php

namespace App\Domain\DTO\Auth\Login;

readonly class AuthLoginInputDto
{
    public function __construct(
        public string $email,
        public string $password,
    ) {
    }
}