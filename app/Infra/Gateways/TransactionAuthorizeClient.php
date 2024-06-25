<?php

namespace App\Infra\Gateways;

use App\Domain\Contracts\Gateways\TransactionAuthorizeInterface;
use App\Domain\Entities\Transaction;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\GuzzleException;
use Psr\Log\LoggerInterface;

readonly class TransactionAuthorizeClient implements TransactionAuthorizeInterface
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
    public function authorize(Transaction $transaction): bool
    {
        try {
            $response = $this->client->request('GET', '', [
                'form_params' => [
                    'wallet_payer_id' => $transaction->payerWalletId,
                    'wallet_payee_id' => $transaction->payeeWalletId,
                    'value'           => $transaction->value
                ]
            ]);
        } catch (GuzzleException $e) {
            $this->logger->error("Error authorizing transaction: {$e->getMessage()}");
            return false;
        }

        $this->logger->info("Transaction authorized successfully. ID: {$transaction->id}");

        $body = json_decode($response->getBody()->getContents(), true, 512, JSON_THROW_ON_ERROR);

        return $body['data']['authorization'];
    }
}