<?php

namespace App\Main\Factories\Infra\Repositories\Eloquent;

use App\Infra\Repositories\Eloquent\TransactionRepository;

class TransactionRepositoryFactory
{
    public static function make(): TransactionRepository
    {
        return new TransactionRepository();
    }
}