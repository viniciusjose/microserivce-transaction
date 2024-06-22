<?php

namespace HyperfTest\Unit\Domain\Entities;

use App\Domain\Entities\Transaction;
use App\Domain\Entities\User;
use App\Infra\Enums\UserType;
use Carbon\Carbon;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[CoversClass(User::class)]
class UserTest extends TestCase
{
    #[Test]
    public function test_it_should_be_return_correct_params(): void
    {
        $user = new User(
            id: 'any_user_id',
            name: 'any_name',
            userType: UserType::SALESMAN,
            email: 'any_email',
            password: 'any_password',
            identify: 'any_identify',
            createdAt: Carbon::now()
        );

        self::assertEquals('any_user_id', $user->id);
        self::assertEquals('any_name', $user->name);
    }

    #[Test]
    public function test_it_should_be_user_can_do_transaction(): void
    {
        $user = new User(
            id: 'any_user_id',
            name: 'any_name',
            userType: UserType::USER,
            email: 'any_email',
            password: 'any_password',
            identify: 'any_identify',
            createdAt: Carbon::now()
        );

        $user->canDoTransaction();

        self::assertEquals('any_user_id', $user->id);
    }

    #[Test]
    public function test_it_should_be_salesman_cant_do_transaction(): void
    {
        $user = new User(
            id: 'any_user_id',
            name: 'any_name',
            userType: UserType::SALESMAN,
            email: 'any_email',
            password: 'any_password',
            identify: 'any_identify',
            createdAt: Carbon::now()
        );

        $this->expectExceptionMessage('Salesman cannot make transactions');

        $user->canDoTransaction();
    }
}