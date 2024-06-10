<?php

namespace App\Domain\DTO\Wallet\store;

readonly class WalletStoreInputDto
{
    public function __construct(
        public float $value = 0.0
    ) {
    }
}