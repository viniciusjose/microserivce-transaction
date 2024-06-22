<?php

namespace App\Domain\Contracts\Gateways;

interface TransactionNotificationInterface
{
    public function notify(): bool;
}