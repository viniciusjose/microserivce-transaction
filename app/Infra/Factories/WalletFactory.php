<?php

declare(strict_types=1);

namespace App\Infra\Factories;

use App\Infra\Entities\Wallet;
use Faker\Factory;
use Faker\Generator;

readonly class WalletFactory
{
    private Generator $faker;

    public function __construct()
    {
        $this->faker = Factory::create();
    }

    public function create(array $attributes = []): Wallet
    {
        return Wallet::create(array_merge([
            'id'      => $this->faker->uuid(),
            'balance' => $this->faker->randomFloat(2, 0, 1000),
        ], $attributes));
    }
}
