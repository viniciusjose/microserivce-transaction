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
        $user = (new UserFactory())->create();
        return Wallet::create(array_merge([
            'id'      => $this->faker->uuid(),
            'user_id' => $user->id,
            'balance' => $this->faker->randomFloat(2, 0, 1000),
        ], $attributes));
    }
}
