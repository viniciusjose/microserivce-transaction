<?php

declare(strict_types=1);

namespace App\Domain\Entities;

use App\Domain\Exceptions\Transaction\NotEnoughBalanceException;
use Carbon\Carbon;
use Decimal\Decimal;

class Wallet
{
    public function __construct(
        public readonly string $id,
        public readonly string $userId,
        public Decimal $balance,
        public readonly Carbon $createdAt,
        public ?Decimal $lastBalance = new Decimal(0),
        public ?Carbon $updatedAt = null,
    ) {
    }

    public function hasEnoughBalance(Decimal $value): void
    {
        if ($this->balance < $value) {
            throw new NotEnoughBalanceException('Payer has no enough balance', 400);
        }
    }

    public function decreaseBalance(Decimal $value): void
    {
        $this->lastBalance = $this->balance;
        $this->balance -= $value;
    }

    public function increaseBalance(Decimal $value): void
    {
        $this->hasEnoughBalance($value);

        $this->lastBalance = $this->balance;
        $this->balance += $value;
    }
}
