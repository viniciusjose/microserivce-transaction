<?php

declare(strict_types=1);
/**
 * This file is part of Hyperf.
 *
 * @link     https://www.hyperf.io
 * @document https://hyperf.wiki
 * @contact  group@hyperf.io
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */

namespace HyperfTest\Feature\Infra\Repositories\Eloquent;

use App\Infra\Factories\WalletFactory;
use App\Infra\Repositories\Eloquent\UserRepository;
use Faker\Factory;
use Faker\Generator;
use Hyperf\Database\Exception\QueryException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class UserRepositoryTest extends TestCase
{
    protected UserRepository $sut;
    protected Generator $faker;

    protected function setUp(): void
    {
        parent::setUp();
        $this->sut = new UserRepository();
        $this->faker = Factory::create();
    }

    public static function userDataProvider(): array
    {
        $faker = Factory::create();

        return [
            [
                [
                    'id'        => $faker->uuid(),
                    'name'      => $faker->name(),
                    'user_type' => 'user',
                    'email'     => 'any@email.com',
                    'password'  => $faker->password(),
                    'identify'  => '123456789',
                    'wallet_id' => $faker->uuid(),
                ],
            ]
        ];
    }

    #[DataProvider('userDataProvider')]
    public function testStore(array $data): void
    {
        (new WalletFactory())->create([
            'id' => $data['wallet_id'],
        ]);

        $user = $this->sut->store($data);

        $this->assertEquals($data['name'], $user->name);
    }

    #[DataProvider('userDataProvider')]
    public function testStoreThrowDuplicateEmail(array $data): void
    {
        $this->expectException(QueryException::class);

        (new WalletFactory())->create([
            'id' => $data['wallet_id'],
        ]);

        $this->sut->store(array_merge($data, ['email' => 'any_email']));

        $this->sut->store(array_merge($data, ['email' => 'any_email', 'identify' => 'any_identify']));
    }

    #[DataProvider('userDataProvider')]
    public function testStoreThrowDuplicateIdentify(array $data): void
    {
        $this->expectException(QueryException::class);

        (new WalletFactory())->create([
            'id' => $data['wallet_id'],
        ]);

        $this->sut->store(array_merge($data, [
            'identify' => 'any_identify'
        ]));

        $this->sut->store(array_merge($data, [
            'identify' => 'any_identify',
            'email'    => 'any_email'
        ]));
    }
}
