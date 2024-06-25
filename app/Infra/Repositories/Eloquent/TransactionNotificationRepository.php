<?php

namespace App\Infra\Repositories\Eloquent;

use App\Domain\Contracts\Repositories\TransactionNotification\TransactionNotificationRepositoryInterface;
use App\Domain\Entities\TransactionNotification;
use App\Infra\Entities\TransactionNotification as Model;

class TransactionNotificationRepository implements TransactionNotificationRepositoryInterface
{

    public function store(TransactionNotification $notification): TransactionNotification
    {
        Model::create([
            'id'             => $notification->id,
            'transaction_id' => $notification->transaction_id,
            'date'           => $notification->date,
            'status'         => $notification->status
        ]);

        return $notification;
    }
}