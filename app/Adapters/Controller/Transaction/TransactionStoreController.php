<?php

namespace App\Adapters\Controller\Transaction;

use App\Adapters\Request\Transaction\TransactionStoreRequest;
use App\Application\UseCases\Transaction\TransactionStoreUseCase;
use App\Domain\DTO\Transaction\TransactionStoreInputDto;
use App\Domain\Exceptions\Transaction\NotEnoughBalanceException;
use App\Domain\Exceptions\Transaction\NotValidTransactionException;
use App\Domain\Exceptions\User\CannotMakeTransactionException;
use App\Domain\Exceptions\User\UserNotFoundException;
use App\Domain\Exceptions\Wallet\WalletNotFoundException;
use App\Main\Factories\Domain\UseCases\Transaction\TransactionStoreUseCaseFactory;
use Hyperf\HttpServer\Contract\ResponseInterface;
use Hyperf\Logger\LoggerFactory;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Log\LoggerInterface;

class TransactionStoreController
{
    protected LoggerInterface $logger;
    protected TransactionStoreUseCase $useCase;

    public function __construct(LoggerFactory $logger)
    {
        $this->useCase = TransactionStoreUseCaseFactory::make();
        $this->logger = $logger->get();
    }

    public function __invoke(TransactionStoreRequest $request, ResponseInterface $response): Response
    {
        $data = $request->validated();

        try {
            $this->useCase->handle(
                new TransactionStoreInputDto(
                    value: $data['value'],
                    payee_id: $data['payee'],
                    payer_id: $data['payer']
                )
            );
        } catch (CannotMakeTransactionException|NotValidTransactionException|UserNotFoundException|WalletNotFoundException|NotEnoughBalanceException $e) {
            return $response->withStatus($e->getCode())->json(['message' => $e->getMessage()]);
        } catch (\Throwable $e) {
            $this->logger->error($e->getMessage());

            return $response->withStatus(500)->json(['message' => $e->getMessage()]);
        }

        return $response->withStatus(201);
    }
}