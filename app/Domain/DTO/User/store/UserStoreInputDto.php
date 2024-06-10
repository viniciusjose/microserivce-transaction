<?php

namespace App\Domain\DTO\User\store;

readonly class UserStoreInputDto
{
    public function __construct(
        public string $name,
        public string $userType,
        public string $email,
        public string $password,
        public string $identify,
    ) {
    }
}