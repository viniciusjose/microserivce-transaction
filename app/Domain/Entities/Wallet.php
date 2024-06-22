<?php

declare(strict_types=1);

namespace App\Domain\Entities;

use App\Domain\Exceptions\Transaction\NotEnoughBalanceException;
use Carbon\Carbon;

class Wallet
{
    public function __construct(
        public readonly string $id,
        public readonly string $userId,
        public float $balance,
        public float $lastBalance,
        public readonly Carbon $createdAt,
        public ?Carbon $updatedAt = null,
    ) {
    }

    public function hasEnoughBalance(float $value): void
    {
        if ($this->balance < $value) {
            throw new NotEnoughBalanceException('Payer has no enough balance', 400);
        }
    }
}
