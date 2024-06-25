<?php

namespace App\Application\UseCases\Transaction;

use App\Domain\Contracts\Gateways\KafkaProduceMessageInterface;
use App\Domain\Contracts\Gateways\TransactionAuthorizeInterface;
use App\Domain\Contracts\Gateways\UuidGeneratorInterface;
use App\Domain\Contracts\Repositories\Transaction\TransactionStoreInterface;
use App\Domain\Contracts\Repositories\User\UserShowInterface;
use App\Domain\Contracts\Repositories\Wallet\WalletGetByUserInterface;
use App\Domain\Contracts\Repositories\Wallet\WalletUpdateBalanceInterface;
use App\Domain\DTO\Transaction\store\TransactionStoreInputDto;
use App\Domain\Entities\Transaction;
use App\Domain\Exceptions\Transaction\NotValidTransactionException;
use App\Domain\Exceptions\User\UserNotFoundException;
use App\Domain\Exceptions\Wallet\WalletNotFoundException;
use Carbon\Carbon;

readonly class TransactionStoreUseCase
{

    public function __construct(
        protected UuidGeneratorInterface $uuidGenerator,
        protected TransactionStoreInterface $transactionRepo,
        protected UserShowInterface $userRepo,
        protected WalletUpdateBalanceInterface|WalletGetByUserInterface $walletRepo,
        protected TransactionAuthorizeInterface $transactionAuthorizationGateway,
        protected KafkaProduceMessageInterface $kafkaProduceMessage
    ) {
    }

    public function handle(TransactionStoreInputDto $data): bool
    {
        if ($data->payer_id === $data->payee_id) {
            throw new NotValidTransactionException(
                'Payer and payee cannot be the same',
                400
            );
        }

        $payer = $this->userRepo->show($data->payer_id);
        $payee = $this->userRepo->show($data->payee_id);

        if ($payer === null) {
            throw new UserNotFoundException('Payer not found', 404);
        }

        if ($payee === null) {
            throw new UserNotFoundException('Payee not found', 404);
        }

        $payer->canDoTransaction();

        $payeeWallet = $this->walletRepo->getByUser($payee->id);

        if ($payeeWallet === null) {
            throw new WalletNotFoundException('Payee wallet not found', 404);
        }

        $payerWallet = $this->walletRepo->getByUser($payer->id);

        if ($payerWallet === null) {
            throw new WalletNotFoundException('Payer wallet not found', 404);
        }

        $payerWallet->hasEnoughBalance($data->value);

        $transaction = new Transaction(
            payerWalletId: $payerWallet->id,
            payeeWalletId: $payeeWallet->id,
            value: $data->value,
            date: Carbon::now(),
            id: $this->uuidGenerator->generate()
        );

        $transaction->checkValue();

        if (!$this->transactionAuthorizationGateway->authorize($transaction)) {
            throw new NotValidTransactionException('Transaction not authorized', 400);
        }

        $payerWallet->decreaseBalance($data->value);
        $payeeWallet->increaseBalance($data->value);

        $this->transactionRepo->store($transaction);

        $this->walletRepo->updateBalance($payerWallet);
        $this->walletRepo->updateBalance($payeeWallet);

        $this->kafkaProduceMessage->produce(
            'notification',
            'transaction',
            [
                'transaction_id' => $transaction->id,
                'payer_id' => $payer->id,
                'payee_id' => $payee->id,
                'value'    => $data->value,
                'date'     => $transaction->date->toDateTimeString()
            ]
        );

        return true;
    }
}