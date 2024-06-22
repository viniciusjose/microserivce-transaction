<?php

namespace App\Domain\Contracts\Gateways;

use App\Domain\Entities\Transaction;

interface TransactionAuthorizeInterface
{
    public function authorize(Transaction $transaction): bool;
}