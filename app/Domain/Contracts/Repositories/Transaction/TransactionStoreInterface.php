<?php

namespace App\Domain\Contracts\Repositories\Transaction;

use App\Domain\Entities\Transaction;

interface TransactionStoreInterface
{
    public function store(Transaction $transaction): Transaction;
}