<?php

declare(strict_types=1);

namespace App\Main\Factories\Infra\Gateways;

use App\Infra\Gateways\TransactionAuthorizeClient;
use App\Infra\Gateways\TransactionNotifyClient;
use GuzzleHttp\Client;
use Hyperf\Logger\LoggerFactory;

use function Hyperf\Config\config;
use function Hyperf\Support\make;

class TransactionNotifyClientFactory
{
    public static function make(): TransactionNotifyClient
    {
        return new TransactionNotifyClient(
            new Client(['base_uri' => config('clients.notification.base_uri')]),
            make(LoggerFactory::class)->get()
        );
    }
}
