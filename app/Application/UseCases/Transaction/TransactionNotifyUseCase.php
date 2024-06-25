<?php

namespace App\Application\UseCases\Transaction;

use App\Domain\Contracts\Gateways\TransactionNotificationClientInterface;
use App\Domain\Contracts\Gateways\UuidGeneratorInterface;
use App\Domain\Contracts\Repositories\TransactionNotification\TransactionNotificationStoreInterface;
use App\Domain\DTO\Transaction\notify\TransactionNotifyInputDto;
use App\Domain\Entities\TransactionNotification;
use App\Domain\Enums\StatusEnum;
use Carbon\Carbon;

class TransactionNotifyUseCase
{
    public function __construct(
        protected UuidGeneratorInterface $uuidGenerator,
        protected TransactionNotificationStoreInterface $transactionNotificationStore,
        protected TransactionNotificationClientInterface $client
    ) {
    }

    public function handle(TransactionNotifyInputDto $input): void
    {
        $notification = new TransactionNotification(
            transaction_id: $input->transaction_id,
            date: Carbon::parse($input->date),
            id: $this->uuidGenerator->generate(),
            status: StatusEnum::DONE
        );


        $success = $this->client->notify($notification);

        if (!$success) {
            $notification->setStatus(StatusEnum::ERROR);
        }

        $this->transactionNotificationStore->store($notification);

        if ($notification->status === StatusEnum::ERROR) {
            throw new \DomainException('Error on notify transaction');
        }
    }
}