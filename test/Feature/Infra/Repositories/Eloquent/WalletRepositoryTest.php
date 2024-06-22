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

use App\Domain\Entities\Wallet;
use App\Infra\Factories\WalletFactory;
use App\Infra\Repositories\Eloquent\WalletRepository;
use Carbon\Carbon;
use Faker\Factory;
use Faker\Generator;
use Hyperf\DbConnection\Db;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[CoversClass(WalletRepository::class), CoversClass(WalletFactory::class)]
class WalletRepositoryTest extends TestCase
{
    protected WalletRepository $sut;
    protected Generator $faker;


    protected function setUp(): void
    {
        parent::setUp();
        Db::beginTransaction();

        $this->sut = new WalletRepository();
        $this->faker = Factory::create();
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        Db::rollBack();
    }

    public static function walletMockDataProvider(): array
    {
        $faker = Factory::create();

        return [
            [
                [
                    'id'           => $faker->uuid(),
                    'balance'      => $faker->randomFloat(2, 0, 1000),
                    'last_balance' => $faker->randomFloat(2, 0, 1000),
                    'user_id'      => $faker->uuid(),
                    'created_at'   => Carbon::now()
                ],
            ]
        ];
    }

    #[DataProvider('walletMockDataProvider')]
    #[Test]
    public function test_store_it_should_be_return_wallet(array $data): void
    {
        $wallet = $this->sut->store($data);

        $this->assertIsString($wallet->id);
        $this->assertEquals($data['balance'], $wallet->balance);
    }

    public function test_update_balance_it_should_be_update_balance_correctly(): void
    {
        $model = (new WalletFactory())->create();

        $wallet = new Wallet(
            id: $model->id,
            userId: $model->user_id,
            balance: (float)$model->balance,
            createdAt: $model->created_at,
            lastBalance: (float)$model->last_balance,
            updatedAt: $model->updatedAt
        );

        $wallet->increaseBalance(100);

        $this->sut->updateBalance($wallet);

        self::assertEquals($wallet->balance, $this->sut->getByUser($wallet->userId)->balance);
    }
}
