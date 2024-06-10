<?php

namespace App\Main\Factories\Domain\UseCases\User;

use App\Domain\UseCases\User\UserStoreUseCase;
use App\Main\Factories\Infra\Gateways\UuidGeneratorFactory;
use App\Main\Factories\Infra\Repositories\Eloquent\UserRepositoryFactory;
use App\Main\Factories\Infra\Repositories\Eloquent\WalletRepositoryFactory;

class UserStoreUseCaseFactory
{
    public static function make(): UserStoreUseCase
    {
        return new UserStoreUseCase(
            UuidGeneratorFactory::make(),
            UserRepositoryFactory::make(),
            WalletRepositoryFactory::make()
        );
    }
}