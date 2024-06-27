<?php

namespace HyperfTest\Unit\Domain\Entities;

use App\Domain\Entities\Transaction;
use Carbon\Carbon;
use Decimal\Decimal;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[CoversClass(Transaction::class)]
class TransactionTest extends TestCase
{
    #[Test]
    public function test_it_should_be_pass_value_verification(): void
    {
        $wallet = new Transaction(
            payerWalletId: 'any_wallet_id',
            payeeWalletId: 'any_user_id',
            value:  new Decimal('100'),
            date: Carbon::now()
        );

        $wallet->checkValue();

        self::assertEquals(100, $wallet->value->toFloat());
    }

    #[Test]
    public function test_it_should_be_throw_if_value_is_incorrectly(): void
    {
        $wallet = new Transaction(
            payerWalletId: 'any_wallet_id',
            payeeWalletId: 'any_user_id',
            value:  new Decimal('0'),
            date: Carbon::now()
        );

        $this->expectExceptionMessage('The value must be greater than 0');

        $wallet->checkValue();
    }
}