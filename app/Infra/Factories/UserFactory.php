<?php

declare(strict_types=1);

namespace App\Infra\Factories;

use App\Infra\Entities\User;
use Carbon\Carbon;
use Faker\Factory;
use Faker\Generator;

readonly class UserFactory
{
    private Generator $faker;

    public function __construct()
    {
        $this->faker = Factory::create();
    }

    public function create(array $attributes = []): User
    {
        $wallet_id = $this->faker->uuid();
        (new WalletFactory())->create(['id' => $wallet_id]);

        return User::create(array_merge([
            'id'         => $this->faker->uuid(),
            'wallet_id'  => $wallet_id,
            'name'       => $this->faker->name(),
            'user_type'  => 'user',
            'email'      => $this->faker->email(),
            'password'   => $this->faker->password(),
            'created_at' => Carbon::now(),
            'identify'   => '11111111111',
        ], $attributes));
    }
}
