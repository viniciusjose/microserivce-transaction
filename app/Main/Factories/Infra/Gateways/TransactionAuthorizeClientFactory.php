<?php

declare(strict_types=1);

namespace App\Main\Factories\Infra\Gateways;

use App\Infra\Gateways\TransactionAuthorizeClient;
use GuzzleHttp\Client;
use Hyperf\Logger\LoggerFactory;

use function Hyperf\Config\config;
use function Hyperf\Support\make;

class TransactionAuthorizeClientFactory
{
    public static function make(): TransactionAuthorizeClient
    {
        return new TransactionAuthorizeClient(
            new Client(['base_uri' => config('clients.authorization.base_uri')]),
            make(LoggerFactory::class)->get()
        );
    }
}
