<?php

namespace App\Domain\UseCases\Transaction;

use App\Domain\Contracts\Repositories\User\UserShowInterface;
use App\Domain\Contracts\Repositories\Wallet\WalletGetByUserInterface;
use App\Domain\DTO\Transaction\TransactionStoreInputDto;
use App\Domain\Entities\Transaction;
use App\Domain\Exceptions\Transaction\NotValidTransactionException;
use App\Domain\Exceptions\User\UserNotFoundException;
use App\Domain\Exceptions\Wallet\WalletNotFoundException;
use Carbon\Carbon;

readonly class TransactionStoreUseCase
{

    public function __construct(
        protected UserShowInterface $userRepo,
        protected WalletGetByUserInterface $walletRepo
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
            date: Carbon::now()
        );

        $transaction->checkValue();

        return true;
    }
}