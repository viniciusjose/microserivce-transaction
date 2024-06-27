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
use App\Domain\Entities\User;
use App\Domain\Entities\Wallet;
use App\Domain\Exceptions\Transaction\NotValidTransactionException;
use App\Domain\Exceptions\User\UserNotFoundException;
use App\Domain\Exceptions\Wallet\WalletNotFoundException;
use Carbon\Carbon;
use Decimal\Decimal;
use Hyperf\Coroutine\Parallel;

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

        $parallel = new Parallel(2);

        $parallel->add(function () use ($data) {
            return $this->userRepo->show($data->payer_id);
        });

        $parallel->add(function () use ($data) {
            return $this->userRepo->show($data->payee_id);
        });

        /**
         * @var User|null $payer
         * @var User|null $payee
         **/
        [$payer, $payee] = $parallel->wait();

        if ($payer === null) {
            throw new UserNotFoundException('Payer not found', 404);
        }

        if ($payee === null) {
            throw new UserNotFoundException('Payee not found', 404);
        }

        $payer->canDoTransaction();

        $parallel = new Parallel(2);

        $parallel->add(function () use ($payer) {
            return $this->walletRepo->getByUser($payer->id);
        });

        $parallel->add(function () use ($payee) {
            return $this->walletRepo->getByUser($payee->id);
        });

        /**
         * @var Wallet|null $payerWallet
         * @var Wallet|null $payeeWallet
         **/
        [$payerWallet, $payeeWallet] = $parallel->wait();

        if ($payeeWallet === null) {
            throw new WalletNotFoundException('Payee wallet not found', 404);
        }

        if ($payerWallet === null) {
            throw new WalletNotFoundException('Payer wallet not found', 404);
        }

        $payerWallet->hasEnoughBalance($data->value);

        $transaction = new Transaction(
            payerWalletId: $payerWallet->id,
            payeeWalletId: $payeeWallet->id,
            value: new Decimal((string)$data->value),
            date: Carbon::now(),
            id: $this->uuidGenerator->generate()
        );

        $transaction->checkValue();

        if (!$this->transactionAuthorizationGateway->authorize($transaction)) {
            throw new NotValidTransactionException('Transaction not authorized', 400);
        }

        $payerWallet->decreaseBalance($transaction->value);
        $payeeWallet->increaseBalance($transaction->value);

        $this->transactionRepo->store($transaction);

        \Hyperf\Coroutine\go(function () use ($payerWallet, $payeeWallet) {
            $this->walletRepo->updateBalance($payerWallet);
            $this->walletRepo->updateBalance($payeeWallet);
        });

        \Hyperf\Coroutine\go(function () use ($transaction, $payer, $payee, $data) {
            $this->kafkaProduceMessage->produce(
                'notification',
                'transaction',
                [
                    'transaction_id' => $transaction->id,
                    'payer_id'       => $payer->id,
                    'payee_id'       => $payee->id,
                    'value'          => $data->value,
                    'date'           => $transaction->date->toDateTimeString()
                ]
            );
        });

        return true;
    }
}