<?php

namespace App\Main\Consumers\Notify;

use App\Application\UseCases\Transaction\TransactionNotifyUseCase;
use App\Domain\DTO\Transaction\notify\TransactionNotifyInputDto;
use App\Main\Factories\Application\UseCases\Transaction\TransactionNotifyUseCaseFactory;
use Hyperf\Kafka\AbstractConsumer;
use Hyperf\Kafka\Annotation\Consumer;
use longlang\phpkafka\Consumer\ConsumeMessage;

#[Consumer(topic: 'notification', groupId: 'microservice_transaction', autoCommit: true)]
class SendNotificationConsumer extends AbstractConsumer
{
    protected TransactionNotifyUseCase $useCase;

    public function __construct()
    {
        $this->useCase = TransactionNotifyUseCaseFactory::make();
    }

    /**
     * @throws \JsonException
     */
    public function consume(ConsumeMessage $message): void
    {
        $value = json_decode($message->getValue(), true, 512, JSON_THROW_ON_ERROR);

        $dto = new TransactionNotifyInputDto(
            transaction_id: $value['transaction_id'],
            payee_id: $value['payee_id'],
            payer_id: $value['payer_id'],
            value: $value['value'],
            date: $value['date']
        );

        $this->useCase->handle($dto);
    }
}