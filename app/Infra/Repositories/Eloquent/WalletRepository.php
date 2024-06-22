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
            id: $wallet->id,
            userId: $wallet->user_id,
            balance: $wallet->balance,
            createdAt: $wallet->created_at,
            updatedAt: $wallet->updated_at,
        );
    }

    public function getByUser(string $userId): ?Wallet
    {
        $wallet = \App\Infra\Entities\Wallet::where('user_id', $userId)->first();

        if (!$wallet) {
            return null;
        }

        return new Wallet(
            id: $wallet->id,
            userId: $wallet->user_id,
            balance: $wallet->balance,
            createdAt: $wallet->created_at,
            updatedAt: $wallet->updated_at,
        );
    }
}
