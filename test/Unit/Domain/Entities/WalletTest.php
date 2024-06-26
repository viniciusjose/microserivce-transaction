<?php

namespace HyperfTest\Unit\Domain\Entities;

use App\Domain\Entities\Wallet;
use Carbon\Carbon;
use Decimal\Decimal;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[CoversClass(Wallet::class)]
class WalletTest extends TestCase
{
    #[Test]
    public function test_it_should_be_return_null_on_enough_balance(): void
    {
        $wallet = new Wallet(
            id: 'any_wallet_id',
            userId: 'any_user_id',
            balance: new Decimal('100'),
            createdAt: Carbon::now()
        );

        self::assertNull($wallet->hasEnoughBalance(new Decimal(50)));
    }

    #[Test]
    public function test_it_should_be_throw_if_balance_is_no_enough(): void
    {
        $wallet = new Wallet(
            id: 'any_wallet_id',
            userId: 'any_user_id',
            balance: new Decimal('100'),
            createdAt: Carbon::now()
        );

        $this->expectExceptionMessage('Payer has no enough balance');
        $wallet->hasEnoughBalance(new Decimal(150));
    }

    public function test_it_should_be_return_wallet_balance_decreased(): void
    {
        $wallet = new Wallet(
            id: 'any_wallet_id',
            userId: 'any_user_id',
            balance: new Decimal('100'),
            createdAt: Carbon::now()
        );

        $wallet->decreaseBalance( new Decimal('50'));

        self::assertEquals(50, $wallet->balance->toFloat());
        self::assertEquals(100, $wallet->lastBalance->toFloat());
    }

    public function test_it_should_be_return_wallet_balance_increased(): void
    {
        $wallet = new Wallet(
            id: 'any_wallet_id',
            userId: 'any_user_id',
            balance: new Decimal('100'),
            createdAt: Carbon::now()
        );

        $wallet->increaseBalance( new Decimal('50'));

        self::assertEquals(150, $wallet->balance->toFloat());
        self::assertEquals(100, $wallet->lastBalance->toFloat());
    }
}