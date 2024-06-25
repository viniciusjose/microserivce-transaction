<?php

namespace App\Main\Factories\Infra\Gateways;

use App\Infra\Gateways\KafkaProducerMessage;
use Hyperf\Kafka\Producer;

use function Hyperf\Support\make;

class KafkaProducerMessageFactory
{
    public static function make(): KafkaProducerMessage
    {
        return new KafkaProducerMessage(
            make(Producer::class)
        );
    }
}