<?php

namespace App\Domain\Contracts\Repositories\Transaction;

use App\Domain\Entities\Transaction;

interface TransactionShowInterface
{
    public function show(string $id): ?Transaction;
}