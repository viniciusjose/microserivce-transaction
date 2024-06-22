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
        return User::create(array_merge([
            'id'         => $this->faker->uuid(),
            'name'       => $this->faker->name(),
            'user_type'  => 'user',
            'email'      => $this->faker->email(),
            'password'   => $this->faker->password(),
            'created_at' => Carbon::now(),
            'identify'   => $this->faker->randomFloat(0, 1000, 9999)
        ], $attributes));
    }
}
