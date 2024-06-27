<?php

namespace HyperfTest\Unit\Application\UseCases\Transaction;

use App\Application\UseCases\Transaction\TransactionStoreUseCase;
use App\Domain\Contracts\Gateways\KafkaProduceMessageInterface;
use App\Domain\Contracts\Gateways\TransactionAuthorizeInterface;
use App\Domain\Contracts\Gateways\UuidGeneratorInterface;
use App\Domain\Contracts\Repositories\Transaction\TransactionRepositoryInterface;
use App\Domain\Contracts\Repositories\User\UserShowInterface;
use App\Domain\Contracts\Repositories\Wallet\WalletRepositoryInterface;
use App\Domain\DTO\Transaction\store\TransactionStoreInputDto;
use App\Domain\Entities\Transaction;
use App\Domain\Entities\User;
use App\Domain\Entities\Wallet;
use App\Domain\Enums\UserType;
use App\Domain\Exceptions\Transaction\InvalidValueException;
use App\Domain\Exceptions\Transaction\NotEnoughBalanceException;
use App\Domain\Exceptions\Transaction\NotValidTransactionException;
use App\Domain\Exceptions\User\CannotMakeTransactionException;
use App\Domain\Exceptions\User\UserNotFoundException;
use App\Domain\Exceptions\Wallet\WalletNotFoundException;
use Carbon\Carbon;
use Decimal\Decimal;
use Faker\Factory;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\TestCase;

use function Hyperf\Coroutine\run;

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
    protected KafkaProduceMessageInterface $kafkaProduceMessageMock;

    protected Transaction $transactionStub;
    protected Wallet $walletStub;
    protected Wallet $walletPayerStub;
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
            balance: new Decimal('100'),
            createdAt: new Carbon()
        );

        $this->walletPayerStub = new Wallet(
            id: $faker->uuid(),
            userId: $this->payerStub->id,
            balance: new Decimal('100'),
            createdAt: new Carbon()
        );

        $this->transactionStub = new Transaction(
            payerWalletId: $this->walletPayerStub->id,
            payeeWalletId: $this->walletStub->id,
            value: new Decimal('100'),
            date: new Carbon(),
            id: $faker->uuid()
        );

        $this->uuidGeneratorMock = $this->createMock(UuidGeneratorInterface::class);
        $this->uuidGeneratorMock->method('generate')->willReturn($faker->uuid());

        $this->transactionRepoMock = $this->createMock(TransactionRepositoryInterface::class);
        $this->transactionRepoMock->method('store')->willReturn($this->transactionStub);

        $this->userRepoMock = $this->createMock(UserShowInterface::class);
        $this->userRepoMock->method('show')
            ->willReturnOnConsecutiveCalls($this->payerStub, $this->userStub);

        $this->walletRepoMock = $this->createMock(WalletRepositoryInterface::class);
        $this->walletRepoMock->method('getByUser')
            ->willReturnOnConsecutiveCalls($this->walletPayerStub, $this->walletStub);
        $this->walletRepoMock->method('updateBalance');

        $this->authorizationGatewayMock = $this->createMock(TransactionAuthorizeInterface::class);
        $this->authorizationGatewayMock->method('authorize')->willReturn(true);

        $this->kafkaProduceMessageMock = $this->createMock(KafkaProduceMessageInterface::class);
        $this->kafkaProduceMessageMock->method('produce');

        $this->sut = new TransactionStoreUseCase(
            $this->uuidGeneratorMock,
            $this->transactionRepoMock,
            $this->userRepoMock,
            $this->walletRepoMock,
            $this->authorizationGatewayMock,
            $this->kafkaProduceMessageMock
        );
    }

    #[Test]
    public function test_it_should_be_do_transaction_correctly(): void
    {
        run(function () {
            $data = new TransactionStoreInputDto(
                value: 100,
                payee_id: $this->userStub->id,
                payer_id: $this->payerStub->id,
            );

            $sutData = $this->sut->handle($data);

            self::assertTrue($sutData);
        });
    }

    /**
     * @throws Exception
     */
    #[Test]
    public function test_it_should_be_throw_if_payer_not_exists(): void
    {
        run(function () {
            $this->userRepoMock = $this->createMock(UserShowInterface::class);
            $this->userRepoMock->method('show')
                ->willReturnOnConsecutiveCalls(null, $this->userStub);

            $sut = $this->makeSut();

            $data = new TransactionStoreInputDto(
                value: 100,
                payee_id: 'any_id',
                payer_id: $this->payerStub->id,
            );


            try {
                $sut->handle($data);
            } catch (UserNotFoundException $e) {
                self::assertEquals('Payer not found', $e->getMessage());
                self::assertEquals(404, $e->getCode());
            }
        });
    }

    /**
     * @throws Exception
     */
    #[Test]
    public function test_it_should_be_throw_if_payee_not_exists(): void
    {
        run(function () {
            $this->userRepoMock = $this->createMock(UserShowInterface::class);
            $this->userRepoMock->method('show')
                ->willReturnOnConsecutiveCalls($this->payerStub, null);

            $sut = $this->makeSut();

            $data = new TransactionStoreInputDto(
                value: 100,
                payee_id: 'any_id',
                payer_id: $this->payerStub->id,
            );

            try {
                $sut->handle($data);
            } catch (UserNotFoundException $e) {
                self::assertEquals('Payee not found', $e->getMessage());
                self::assertEquals(404, $e->getCode());
            }
        });
    }

    /**
     * @throws Exception
     */
    #[Test]
    public function test_it_should_be_throw_if_wallet_payee_not_exists(): void
    {
        $this->walletRepoMock = $this->createMock(WalletRepositoryInterface::class);
        $this->walletRepoMock->method('getByUser')
            ->willReturnOnConsecutiveCalls($this->walletPayerStub, null);
        run(function () {
            $sut = $this->makeSut();

            $data = new TransactionStoreInputDto(
                value: 100,
                payee_id: $this->userStub->id,
                payer_id: $this->payerStub->id,
            );

            try {
                $sut->handle($data);
            } catch (WalletNotFoundException $e) {
                self::assertEquals('Payee wallet not found', $e->getMessage());
                self::assertEquals(404, $e->getCode());
            }
        });
    }

    /**
     * @throws Exception
     */
    #[Test]
    public function test_it_should_be_throw_if_wallet_payer_not_exists(): void
    {
        $this->walletRepoMock = $this->createMock(WalletRepositoryInterface::class);
        $this->walletRepoMock->method('getByUser')
            ->willReturnOnConsecutiveCalls(null, $this->walletStub);
        run(function () {
            $sut = $this->makeSut();

            $data = new TransactionStoreInputDto(
                value: 100,
                payee_id: $this->userStub->id,
                payer_id: $this->payerStub->id,
            );

            try {
                $sut->handle($data);
            } catch (WalletNotFoundException $e) {
                self::assertEquals('Payer wallet not found', $e->getMessage());
                self::assertEquals(404, $e->getCode());
            }
        });
    }

    #[Test]
    public function test_it_should_be_throw_if_value_sent_is_zero(): void
    {
        run(function () {
            $data = new TransactionStoreInputDto(
                value: 0,
                payee_id: 'any_id',
                payer_id: $this->payerStub->id,
            );

            try {
                $this->sut->handle($data);
            } catch (InvalidValueException $e) {
                self::assertEquals('The value must be greater than 0', $e->getMessage());
                self::assertEquals(400, $e->getCode());
            }
        });
    }

    #[Test]
    public function test_it_should_be_throw_if_payer_has_no_enough_balance(): void
    {
        run(function () {
            $data = new TransactionStoreInputDto(
                value: 101,
                payee_id: $this->userStub->id,
                payer_id: $this->payerStub->id,
            );

            try {
                $this->sut->handle($data);
            } catch (NotEnoughBalanceException $e) {
                self::assertEquals('Payer has no enough balance', $e->getMessage());
                self::assertEquals(400, $e->getCode());
            }
        });
    }

    #[Test]
    public function test_it_should_be_throw_if_payer_is_the_same_on_payee(): void
    {
        run(function () {
            $data = new TransactionStoreInputDto(
                value: 100,
                payee_id: $this->userStub->id,
                payer_id: $this->userStub->id,
            );

            try {
                $this->sut->handle($data);
            } catch (NotValidTransactionException $e) {
                self::assertEquals('Payer and payee cannot be the same', $e->getMessage());
                self::assertEquals(400, $e->getCode());
            }
        });
    }

    /**
     * @throws Exception
     */
    #[Test]
    public function test_it_should_be_throw_if_payer_is_the_salesman(): void
    {
        run(function () {
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
                $this->authorizationGatewayMock,
                $this->kafkaProduceMessageMock
            );

            try {
                $sut->handle($data);
            } catch (CannotMakeTransactionException $e) {
                self::assertEquals('Salesman cannot make transactions', $e->getMessage());
                self::assertEquals(400, $e->getCode());
            }
        });
    }

    /**
     * @throws Exception
     */
    #[Test]
    public function test_it_should_be_throw_if_transaction_is_not_authorized(): void
    {
        run(function () {
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

            $sut = new TransactionStoreUseCase(
                $this->uuidGeneratorMock,
                $this->transactionRepoMock,
                $this->userRepoMock,
                $this->walletRepoMock,
                $authorizeMock,
                $this->kafkaProduceMessageMock
            );

            try {
                $sut->handle($data);
            } catch (NotValidTransactionException $e) {
                self::assertEquals('Transaction not authorized', $e->getMessage());
                self::assertEquals(400, $e->getCode());
            }
        });
    }

    private function makeSut(): TransactionStoreUseCase
    {
        return new TransactionStoreUseCase(
            $this->uuidGeneratorMock,
            $this->transactionRepoMock,
            $this->userRepoMock,
            $this->walletRepoMock,
            $this->authorizationGatewayMock,
            $this->kafkaProduceMessageMock
        );
    }
}