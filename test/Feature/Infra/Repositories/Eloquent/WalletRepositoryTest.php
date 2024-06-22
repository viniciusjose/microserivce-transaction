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
use App\Infra\Repositories\Eloquent\WalletRepository;
use Carbon\Carbon;
use Faker\Factory;
use Faker\Generator;
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
        $this->sut = new WalletRepository();
        $this->faker = Factory::create();
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
}
