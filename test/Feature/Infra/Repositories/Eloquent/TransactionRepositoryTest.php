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

use App\Domain\Entities\Transaction;
use App\Infra\Entities\Wallet as WalletModel;
use App\Infra\Factories\TransactionFactory;
use App\Infra\Factories\WalletFactory;
use App\Infra\Repositories\Eloquent\TransactionRepository;
use Carbon\Carbon;
use Decimal\Decimal;
use Faker\Factory;
use Faker\Generator;
use Hyperf\DbConnection\Db;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(TransactionRepository::class), CoversClass(TransactionFactory::class)]
#[UsesClass(WalletFactory::class), UsesClass(Transaction::class)]
class TransactionRepositoryTest extends TestCase
{
    protected TransactionRepository $sut;
    protected Generator $faker;
    protected WalletModel $payeeWallet;
    protected WalletModel $payerWallet;


    protected function setUp(): void
    {
        parent::setUp();
        Db::beginTransaction();

        $this->sut = new TransactionRepository();
        $this->faker = Factory::create();

        $this->payeeWallet = (new WalletFactory())->create();
        $this->payerWallet = (new WalletFactory())->create();
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        Db::rollBack();
    }


    #[Test]
    public function test_store_it_should_be_return_transaction(): void
    {
        $entity = new Transaction(
            payerWalletId: $this->payerWallet->id,
            payeeWalletId: $this->payeeWallet->id,
            value: new Decimal(100),
            date: Carbon::now(),
            id: $this->faker->uuid()
        );

        $transaction = $this->sut->store($entity);

        $this->assertIsString($transaction->id);
        $this->assertEquals($entity->value, $transaction->value);
    }

    #[Test]
    public function test_show_it_should_be_return_transaction(): void
    {
        $entity = (new TransactionFactory())->create();

        $transaction = $this->sut->show($entity->id);

        $this->assertEquals($entity->id, $transaction->id);
    }


    #[Test]
    public function test_show_it_should_be_return_null_if_not_find_transaction(): void
    {
        $transaction = $this->sut->show('any_id');

        $this->assertNull($transaction);
    }
}
