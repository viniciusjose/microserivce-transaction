<?php

namespace App\Infra\Gateways;

use App\Domain\Contracts\Gateways\TransactionAuthorizeInterface;
use App\Domain\Entities\Transaction;

class TransactionAuthorize implements TransactionAuthorizeInterface
{

    public function authorize(Transaction $transaction): bool
    {
        return true;
    }
}