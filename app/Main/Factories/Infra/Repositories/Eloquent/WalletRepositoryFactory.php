<?php

namespace App\Main\Factories\Infra\Repositories\Eloquent;

use App\Infra\Repositories\Eloquent\WalletRepository;

class WalletRepositoryFactory
{
    public static function make(): WalletRepository
    {
        return new WalletRepository();
    }
}