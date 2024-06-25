<?php

namespace App\Domain\Contracts\Gateways;

interface KafkaProduceMessageInterface
{
    public function produce(string $topic, string $key, array $message): void;
}