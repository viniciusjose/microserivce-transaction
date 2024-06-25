<?php

namespace App\Main\Factories\Application\UseCases\Transaction;

use App\Application\UseCases\Transaction\TransactionStoreUseCase;
use App\Main\Factories\Infra\Gateways\TransactionAuthorizeClientFactory;
use App\Main\Factories\Infra\Gateways\UuidGeneratorFactory;
use App\Main\Factories\Infra\Repositories\Eloquent\TransactionRepositoryFactory;
use App\Main\Factories\Infra\Repositories\Eloquent\UserRepositoryFactory;
use App\Main\Factories\Infra\Repositories\Eloquent\WalletRepositoryFactory;

class TransactionStoreUseCaseFactory
{
    public static function make(): TransactionStoreUseCase
    {
        return new TransactionStoreUseCase(
            UuidGeneratorFactory::make(),
            TransactionRepositoryFactory::make(),
            UserRepositoryFactory::make(),
            WalletRepositoryFactory::make(),
            TransactionAuthorizeClientFactory::make()
        );
    }
}