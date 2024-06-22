<?php

namespace HyperfTest\Unit\Domain\UseCases\Transaction;

use App\Domain\Contracts\Repositories\User\UserRepositoryInterface;
use App\Domain\Contracts\Repositories\User\UserShowInterface;
use App\Domain\Contracts\Repositories\Wallet\WalletGetByUserInterface;
use App\Domain\DTO\Transaction\TransactionStoreInputDto;
use App\Domain\Entities\User;
use App\Domain\Entities\Wallet;
use App\Domain\Exceptions\Transaction\InvalidValueException;
use App\Domain\Exceptions\Transaction\NotEnoughBalanceException;
use App\Domain\Exceptions\Transaction\NotValidTransactionException;
use App\Domain\Exceptions\User\CannotMakeTransactionException;
use App\Domain\Exceptions\User\UserNotFoundException;
use App\Domain\UseCases\Transaction\TransactionStoreUseCase;
use App\Infra\Enums\UserType;
use Carbon\Carbon;
use Faker\Factory;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\TestCase;

/**
 * @property User $userStub
 */
#[CoversClass(TransactionStoreUseCase::class)]
class TransactionStoreUseCaseTest extends TestCase
{
    protected TransactionStoreUseCase $sut;
    protected WalletGetByUserInterface $walletRepoMock;
    protected User $userStub;
    protected Wallet $walletStub;
    protected User $payerStub;

    /**
     * @throws Exception
     */
    protected function setUp(): void
    {
        parent::setUp();
        $faker = Factory::create();

        $this->userStub = new User(
            id: $faker->uuid(),
            name: $faker->name(),
            userType: UserType::USER,
            email: $faker->valid()->email(),
            password: $faker->password(),
            identify: '123456789',
            createdAt: new Carbon()
        );

        $this->payerStub = new User(
            id: $faker->uuid(),
            name: $faker->name(),
            userType: UserType::USER,
            email: $faker->valid()->email(),
            password: $faker->password(),
            identify: '123456789',
            createdAt: new Carbon()
        );

        $this->walletStub = new Wallet(
            id: $faker->uuid(),
            userId: $this->userStub->id,
            balance: 100,
            createdAt: new Carbon()
        );

        $userRepoMock = $this->createMock(UserShowInterface::class);
        $userRepoMock->method('show')->willReturn($this->userStub);

        $this->walletRepoMock = $this->createMock(WalletGetByUserInterface::class);
        $this->walletRepoMock->method('getByUser')->willReturn($this->walletStub);
        $this->sut = new TransactionStoreUseCase($userRepoMock, $this->walletRepoMock);
    }

    #[Test]
    public function test_it_should_be_do_transaction_correctly(): void
    {
        $data = new TransactionStoreInputDto(
            value: 100,
            payee_id: $this->userStub->id,
            payer_id: $this->payerStub->id,
        );

        $sutData = $this->sut->handle($data);

        self::assertTrue($sutData);
    }

    /**
     * @throws Exception
     */
    #[Test]
    public function test_it_should_be_throw_if_payee_not_exists(): void
    {
        $this->expectException(UserNotFoundException::class);

        $mock = $this->createMock(UserRepositoryInterface::class);
        $mock->method('show')->willReturn(null);
        $sut = new TransactionStoreUseCase($mock, $this->walletRepoMock);

        $data = new TransactionStoreInputDto(
            value: 100,
            payee_id: 'any_id',
            payer_id: $this->payerStub->id,
        );

        $sut->handle($data);
    }

    #[Test]
    public function test_it_should_be_throw_if_value_sent_is_zero(): void
    {
        $this->expectException(InvalidValueException::class);

        $data = new TransactionStoreInputDto(
            value: 0,
            payee_id: 'any_id',
            payer_id: $this->payerStub->id,
        );

        $this->sut->handle($data);
    }

    #[Test]
    public function test_it_should_be_throw_if_payer_has_no_enough_balance(): void
    {
        $this->expectException(NotEnoughBalanceException::class);

        $data = new TransactionStoreInputDto(
            value: 101,
            payee_id: $this->userStub->id,
            payer_id: $this->payerStub->id,
        );

        $this->sut->handle($data);
    }

    #[Test]
    public function test_it_should_be_throw_if_payer_is_the_same_on_payee(): void
    {
        $this->expectException(NotValidTransactionException::class);

        $data = new TransactionStoreInputDto(
            value: 100,
            payee_id: $this->userStub->id,
            payer_id: $this->userStub->id,
        );

        $this->sut->handle($data);
    }

    /**
     * @throws Exception
     */
    #[Test]
    public function test_it_should_be_throw_if_payer_is_the_salesman(): void
    {
        $this->expectException(CannotMakeTransactionException::class);

        $payer = new User(
            id: 'any_id',
            name: 'any_name',
            userType: UserType::SALESMAN,
            email: 'any_email',
            password: 'any_password',
            identify: 'any_identify',
            createdAt: new Carbon()
        );

        $data = new TransactionStoreInputDto(
            value: 100,
            payee_id: $this->userStub->id,
            payer_id: $payer->id,
        );

        $userRepoMock = $this->createMock(UserShowInterface::class);
        $userRepoMock->method('show')->willReturn($payer);

        $sut = new TransactionStoreUseCase($userRepoMock, $this->walletRepoMock);
        $sut->handle($data);
    }
}