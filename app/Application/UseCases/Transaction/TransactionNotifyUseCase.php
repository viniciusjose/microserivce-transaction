<?php

namespace App\Application\UseCases\Transaction;

use App\Domain\Contracts\Gateways\TransactionNotificationClientInterface;
use App\Domain\Contracts\Gateways\UuidGeneratorInterface;
use App\Domain\Contracts\Repositories\Transaction\TransactionShowInterface;
use App\Domain\Contracts\Repositories\TransactionNotification\TransactionNotificationStoreInterface;
use App\Domain\DTO\Transaction\notify\TransactionNotifyInputDto;
use App\Domain\Entities\TransactionNotification;
use App\Domain\Enums\StatusEnum;
use App\Domain\Exceptions\Transaction\TransactionNotFoundException;
use Carbon\Carbon;

class TransactionNotifyUseCase
{
    public function __construct(
        protected UuidGeneratorInterface $uuidGenerator,
        protected TransactionShowInterface $transactionRepo,
        protected TransactionNotificationStoreInterface $transactionNotificationStore,
        protected TransactionNotificationClientInterface $client
    ) {
    }

    public function handle(TransactionNotifyInputDto $input): bool
    {
        $transaction = $this->transactionRepo->show($input->transaction_id);

        if ($transaction === null) {
            throw new TransactionNotFoundException('Transaction not found', 404);
        }

        $notification = new TransactionNotification(
            transaction_id: $input->transaction_id,
            date: Carbon::parse($input->date),
            id: $this->uuidGenerator->generate(),
            status: StatusEnum::DONE
        );

        if (!$this->client->notify($notification)) {
            $notification->setStatus(StatusEnum::ERROR);
        }

        $this->transactionNotificationStore->store($notification);

        if ($notification->status === StatusEnum::ERROR) {
            throw new \DomainException('Error on notify transaction');
        }

        return true;
    }
}