<?php

namespace HyperfTest\Unit\Application\UseCases\Transaction;

use App\Application\UseCases\Transaction\TransactionStoreUseCase;
use App\Domain\Contracts\Gateways\TransactionAuthorizeInterface;
use App\Domain\Contracts\Gateways\UuidGeneratorInterface;
use App\Domain\Contracts\Repositories\Transaction\TransactionRepositoryInterface;
use App\Domain\Contracts\Repositories\User\UserRepositoryInterface;
use App\Domain\Contracts\Repositories\User\UserShowInterface;
use App\Domain\Contracts\Repositories\Wallet\WalletRepositoryInterface;
use App\Domain\DTO\Transaction\TransactionStoreInputDto;
use App\Domain\Entities\Transaction;
use App\Domain\Entities\User;
use App\Domain\Entities\Wallet;
use App\Domain\Enums\UserType;
use App\Domain\Exceptions\Transaction\InvalidValueException;
use App\Domain\Exceptions\Transaction\NotEnoughBalanceException;
use App\Domain\Exceptions\Transaction\NotValidTransactionException;
use App\Domain\Exceptions\User\CannotMakeTransactionException;
use App\Domain\Exceptions\User\UserNotFoundException;
use Carbon\Carbon;
use Faker\Factory;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\TestCase;

/**
 * @property User $userStub
 */
#[CoversClass(TransactionStoreUseCase::class), CoversClass(TransactionStoreInputDto::class)]
#[UsesClass(User::class), UsesClass(Wallet::class), UsesClass(UserType::class)]
#[UsesClass(UserNotFoundException::class), UsesClass(InvalidValueException::class), UsesClass(NotEnoughBalanceException::class)]
class TransactionStoreUseCaseTest extends TestCase
{
    protected TransactionStoreUseCase $sut;

    protected UserShowInterface $userRepoMock;
    protected WalletRepositoryInterface $walletRepoMock;
    protected TransactionAuthorizeInterface $authorizationGatewayMock;
    protected TransactionRepositoryInterface $transactionRepoMock;
    protected UuidGeneratorInterface $uuidGeneratorMock;

    protected Transaction $transactionStub;
    protected Wallet $walletStub;
    protected User $payerStub;
    protected User $userStub;

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

        $walletPayerStub = new Wallet(
            id: $faker->uuid(),
            userId: $this->payerStub->id,
            balance: 100,
            createdAt: new Carbon()
        );

        $this->transactionStub = new Transaction(
            payerWalletId: $walletPayerStub->id,
            payeeWalletId: $this->walletStub->id,
            value: 100,
            date: new Carbon(),
            id: $faker->uuid()
        );

        $this->uuidGeneratorMock = $this->createMock(UuidGeneratorInterface::class);
        $this->uuidGeneratorMock->method('generate')->willReturn($faker->uuid());

        $this->transactionRepoMock = $this->createMock(TransactionRepositoryInterface::class);
        $this->transactionRepoMock->method('store')->willReturn($this->transactionStub);

        $this->userRepoMock = $this->createMock(UserShowInterface::class);
        $this->userRepoMock->method('show')->willReturn($this->userStub);

        $this->walletRepoMock = $this->createMock(WalletRepositoryInterface::class);
        $this->walletRepoMock->method('getByUser')->willReturn($this->walletStub);
        $this->walletRepoMock->method('updateBalance');

        $this->authorizationGatewayMock = $this->createMock(TransactionAuthorizeInterface::class);
        $this->authorizationGatewayMock->method('authorize')->willReturn(true);

        $this->sut = new TransactionStoreUseCase(
            $this->uuidGeneratorMock,
            $this->transactionRepoMock,
            $this->userRepoMock,
            $this->walletRepoMock,
            $this->authorizationGatewayMock
        );
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

        $sut = new TransactionStoreUseCase(
            $this->uuidGeneratorMock,
            $this->transactionRepoMock,
            $mock,
            $this->walletRepoMock,
            $this->authorizationGatewayMock
        );

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

        $sut = new TransactionStoreUseCase(
            $this->uuidGeneratorMock,
            $this->transactionRepoMock,
            $userRepoMock,
            $this->walletRepoMock,
            $this->authorizationGatewayMock
        );

        $sut->handle($data);
    }

    /**
     * @throws Exception
     */
    #[Test]
    public function test_it_should_be_throw_if_transaction_is_not_authorized(): void
    {
        $payer = new User(
            id: 'any_id',
            name: 'any_name',
            userType: UserType::USER,
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

        $authorizeMock = $this->createMock(TransactionAuthorizeInterface::class);
        $authorizeMock->method('authorize')->willReturn(false);

        $this->expectException(NotValidTransactionException::class);

        $sut = new TransactionStoreUseCase(
            $this->uuidGeneratorMock,
            $this->transactionRepoMock,
            $this->userRepoMock,
            $this->walletRepoMock,
            $authorizeMock
        );

        $sut->handle($data);
    }
}