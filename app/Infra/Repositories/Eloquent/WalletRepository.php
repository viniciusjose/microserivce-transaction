<?php

declare(strict_types=1);

namespace App\Infra\Repositories\Eloquent;

use App\Domain\Contracts\Repositories\Wallet\WalletRepositoryInterface;
use App\Domain\Entities\Wallet;

class WalletRepository implements WalletRepositoryInterface
{
    public function store(?array $data = null): Wallet
    {
        $wallet = \App\Infra\Entities\Wallet::create($data);

        return new Wallet(
            id: $data['id'],
            balance: $wallet->balance,
            lastBalance: $wallet->last_balance,
            createdAt: $wallet->created_at,
            updatedAt: $wallet->updated_at,
        );
    }
}
