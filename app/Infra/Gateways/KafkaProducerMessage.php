<?php

namespace App\Infra\Gateways;

use App\Domain\Contracts\Gateways\KafkaProduceMessageInterface;
use Hyperf\Kafka\Producer;
use JsonException;

class KafkaProducerMessage implements KafkaProduceMessageInterface
{
    public function __construct(
        protected Producer $producer
    ) {
    }

    /**
     * @throws JsonException
     */
    public function produce(string $topic, string $key, array $message): void
    {
        $encoded = json_encode($message, JSON_THROW_ON_ERROR);

        $this->producer->send($topic, $encoded, $key);
    }
}