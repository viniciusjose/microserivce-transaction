<?php

namespace App\Infra\Gateways;

use App\Domain\Contracts\Gateways\TransactionNotificationInterface;

class TransactionNotify implements TransactionNotificationInterface
{

    public function notify(): bool
    {
        // TODO: Implement authorize() method.
    }
}