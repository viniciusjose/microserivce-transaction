<?php

namespace App\Domain\Contracts\Gateways;

interface KafkaProduceMessageInterface
{
    public function produce(): void;
}