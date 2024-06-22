<?php

namespace App\Domain\UseCases\Transaction;

use App\Domain\Contracts\Repositories\User\UserShowInterface;
use App\Domain\DTO\Transaction\TransactionStoreInputDto;
use App\Domain\Exceptions\Transaction\InvalidValueException;
use App\Domain\Exceptions\User\UserNotFoundException;

readonly class TransactionStoreUseCase
{

    public function __construct(
        protected UserShowInterface $userRepo,
    ) {
    }

    public function handle(TransactionStoreInputDto $data): bool
    {
        $payee = $this->userRepo->show($data->payee_id);
        $payer = $this->userRepo->show($data->payer_id);

        if ($payee === null) {
            throw new UserNotFoundException('Payee not found', 404);
        }

        if ($payer === null) {
            throw new UserNotFoundException('Payer not found', 404);
        }

        if ($data->value <= 0) {
            throw new InvalidValueException('Invalid value', 400);
        }

        return true;
    }
}