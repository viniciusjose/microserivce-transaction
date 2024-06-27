<?php

declare(strict_types=1);

namespace App\Infra\Factories;

use App\Infra\Entities\Transaction;
use App\Infra\Entities\Wallet;
use Carbon\Carbon;
use Faker\Factory;
use Faker\Generator;

readonly class TransactionFactory
{
    private Generator $faker;

    public function __construct()
    {
        $this->faker = Factory::create();
    }

    public function create(array $attributes = []): Transaction
    {
        $walletPayer = (new WalletFactory())->create();
        $walletPayee = (new WalletFactory())->create();

        return Transaction::create(array_merge([
            'id'              => $this->faker->uuid(),
            'wallet_payer_id' => $walletPayer->id,
            'wallet_payee_id' => $walletPayee->id,
            'value'           => 100,
            'created_at'            => Carbon::now()
        ], $attributes));
    }
}
