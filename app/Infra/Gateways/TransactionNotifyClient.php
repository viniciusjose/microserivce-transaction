<?php

namespace App\Infra\Gateways;

use App\Domain\Contracts\Gateways\TransactionNotificationClientInterface;
use App\Domain\Entities\TransactionNotification;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\GuzzleException;
use Psr\Log\LoggerInterface;

class TransactionNotifyClient implements TransactionNotificationClientInterface
{
    public function __construct(
        protected ClientInterface $client,
        protected LoggerInterface $logger

    ) {
    }

    /**
     * @throws GuzzleException
     * @throws \JsonException
     */
    public function notify(TransactionNotification $notification): bool
    {
        $body = json_encode([
            'transaction_id' => $notification->transaction_id,
            'date'           => $notification->date->toDateString()
        ], JSON_THROW_ON_ERROR);

        try {
            $this->client->request('POST', 'notify', [
                'body' => $body
            ]);
        } catch (GuzzleException $e) {
            $this->logger->error("Error transaction notify: {$e->getMessage()}");

            return false;
        }

        $this->logger->info("Transaction notified successfully. ID: {$notification->transaction_id}");

        return true;
    }
}