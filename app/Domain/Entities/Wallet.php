<?php

declare(strict_types=1);

namespace App\Domain\Entities;

use Carbon\Carbon;

readonly class Wallet
{
    public function __construct(
        public string $id,
        public float $balance,
        public float $lastBalance,
        public Carbon $createdAt,
        public ?Carbon $updatedAt = null,
    ) {
    }
}
