<?php

declare(strict_types=1);

namespace App\Infra\Repositories\Eloquent;

use App\Domain\Contracts\Repositories\Wallet\WalletRepositoryInterface;
use App\Domain\Entities\Wallet;
use App\Infra\Entities\Wallet as Model;
use Carbon\Carbon;
use Decimal\Decimal;
use Hyperf\DbConnection\Db;

class WalletRepository implements WalletRepositoryInterface
{
    public function store(?array $data = null): Wallet
    {
        $wallet = Model::create($data);

        return new Wallet(
            id: $wallet->id,
            userId: $wallet->user_id,
            balance: new Decimal($wallet->balance),
            createdAt: $wallet->created_at,
            lastBalance: new Decimal(0),
            updatedAt: $wallet->updated_at,
        );
    }

    public function getByUser(string $userId): ?Wallet
    {
        $wallet = Model::where('user_id', $userId)->first();

        if (!$wallet) {
            return null;
        }

        return new Wallet(
            id: $wallet->id,
            userId: $wallet->user_id,
            balance: new Decimal($wallet->balance),
            createdAt: $wallet->created_at,
            lastBalance: new Decimal($wallet->last_balance),
            updatedAt: $wallet->updated_at,
        );
    }

    public function updateBalance(Wallet $wallet): void
    {
        Db::table('wallets')
            ->where('id', $wallet->id)
            ->update([
                'balance'      => $wallet->balance,
                'last_balance' => $wallet->lastBalance,
                'updated_at'   => Carbon::now(),
            ]);
    }
}
