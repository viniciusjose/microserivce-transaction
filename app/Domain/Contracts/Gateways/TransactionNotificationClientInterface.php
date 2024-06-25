<?php

namespace App\Domain\Contracts\Gateways;

use App\Domain\Entities\TransactionNotification;

interface TransactionNotificationClientInterface
{
    public function notify(TransactionNotification $notification): bool;
}