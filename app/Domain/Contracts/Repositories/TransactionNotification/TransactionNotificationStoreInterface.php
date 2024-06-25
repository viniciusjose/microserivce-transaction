<?php

namespace App\Domain\Contracts\Repositories\TransactionNotification;

use App\Domain\Entities\TransactionNotification;

interface TransactionNotificationStoreInterface
{
    public function store(TransactionNotification $notification): TransactionNotification;
}