<?php

namespace App\Infra\Repositories\Eloquent;

use App\Domain\Contracts\Repositories\Transaction\TransactionRepositoryInterface;
use App\Domain\Entities\Transaction;
use App\Infra\Entities\Transaction as Model;

class TransactionRepository implements TransactionRepositoryInterface
{

    public function store(Transaction $transaction): Transaction
    {
        $model = new Model();
        $model->id = $transaction->id;
        $model->wallet_payer_id = $transaction->payerWalletId;
        $model->wallet_payee_id = $transaction->payeeWalletId;
        $model->value = $transaction->value;
        $model->created_at = $transaction->date;
        $model->save();

        return $transaction;
    }
}