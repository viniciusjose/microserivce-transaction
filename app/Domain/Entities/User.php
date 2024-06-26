<?php

declare(strict_types=1);

namespace App\Domain\Entities;

use App\Domain\Enums\UserType;
use App\Domain\Exceptions\User\CannotMakeTransactionException;
use Carbon\Carbon;

readonly class User
{
    public function __construct(
        public string $id,
        public string $name,
        public UserType $userType,
        public string $email,
        public string $password,
        public string $identify,
        public Carbon $createdAt,
        public Carbon|null $updatedAt = null,
    ) {
    }

    public function canDoTransaction(): void
    {
        if ($this->userType === UserType::SALESMAN) {
            throw new CannotMakeTransactionException(
                'Salesman cannot make transactions',
                400
            );
        }
    }
}
