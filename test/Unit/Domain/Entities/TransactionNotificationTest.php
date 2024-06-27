<?php

namespace HyperfTest\Unit\Domain\Entities;

use App\Domain\Entities\TransactionNotification;
use App\Domain\Enums\StatusEnum;
use Carbon\Carbon;
use Decimal\Decimal;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[CoversClass(TransactionNotification::class)]
class TransactionNotificationTest extends TestCase
{
    #[Test]
    public function test_it_should_be_create_entity(): void
    {
        $notification = new TransactionNotification(
            transaction_id: 'any_transaction_id',
            date: Carbon::now(),
            id: 'any_id',
            status: StatusEnum::DONE
        );

        self::assertEquals('any_id', $notification->id);
        self::assertEquals(StatusEnum::DONE, $notification->status);
    }

    #[Test]
    public function test_it_should_be_update_status(): void
    {
        $notification = new TransactionNotification(
            transaction_id: 'any_transaction_id',
            date: Carbon::now(),
            id: 'any_id',
            status: StatusEnum::ERROR
        );

        $notification->setStatus(StatusEnum::DONE);

        self::assertEquals(StatusEnum::DONE, $notification->status);
    }
}