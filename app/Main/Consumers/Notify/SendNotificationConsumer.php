<?php

namespace App\Main\Consumers\Notify;

use Hyperf\Kafka\AbstractConsumer;
use Hyperf\Kafka\Annotation\Consumer;
use longlang\phpkafka\Consumer\ConsumeMessage;

#[Consumer(topic: 'microservice_transaction.send_notification', groupId: 'microservice_transaction', autoCommit: true)]
class SendNotificationConsumer extends AbstractConsumer
{
    public function consume(ConsumeMessage $message): void
    {
        // TODO: Implement consume() method.
    }
}