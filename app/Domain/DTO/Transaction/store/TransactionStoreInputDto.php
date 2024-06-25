<?php

namespace App\Domain\DTO\Transaction\store;

readonly class TransactionStoreInputDto
{
    public function __construct(
        public float $value,
        public string $payee_id,
        public string $payer_id,
    ) {
    }
}