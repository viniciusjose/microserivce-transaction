<?php

namespace App\Main\Factories\Application\UseCases\Transaction;

use App\Application\UseCases\Transaction\TransactionNotifyUseCase;
use App\Main\Factories\Infra\Gateways\TransactionNotifyClientFactory;
use App\Main\Factories\Infra\Gateways\UuidGeneratorFactory;
use App\Main\Factories\Infra\Repositories\Eloquent\TransactionNotificationRepositoryFactory;

class TransactionNotifyUseCaseFactory
{
    public static function make(): TransactionNotifyUseCase
    {
        return new TransactionNotifyUseCase(
            UuidGeneratorFactory::make(),
            TransactionNotificationRepositoryFactory::make(),
            TransactionNotifyClientFactory::make()
        );
    }
}