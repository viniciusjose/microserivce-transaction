<?php

namespace App\Domain\DTO\Transaction\notify;

class TransactionNotifyInputDto
{
    public function __construct(
        public string $transaction_id,
        public string $payee_id,
        public string $payer_id,
        public float $value,
        public string $date
    ) {
    }
}