<?php

namespace App\Main\Factories\Infra\Repositories\Eloquent;

use App\Infra\Repositories\Eloquent\TransactionNotificationRepository;

class TransactionNotificationRepositoryFactory
{
    public static function make(): TransactionNotificationRepository
    {
        return new TransactionNotificationRepository();
    }
}