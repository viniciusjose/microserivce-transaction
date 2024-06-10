<?php

namespace App\Domain\DTO\Wallet\store;

readonly class WalletStoreOutputDto
{
    public function __construct(
        public int $id,
        public float $balance,
        public float $lastBalance,
        public string $createdAt,
        public string $updatedAt
    ) {
    }
}