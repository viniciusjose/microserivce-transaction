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

namespace HyperfTest\Unit\Domain\UseCases\User;

use App\Domain\Contracts\Gateways\UuidGeneratorInterface;
use App\Domain\Contracts\Repositories\User\UserRepositoryInterface;
use App\Domain\Contracts\Repositories\Wallet\WalletRepositoryInterface;
use App\Domain\DTO\User\store\UserStoreInputDto;
use App\Domain\DTO\User\store\UserStoreOutputDto;
use App\Domain\Entities\User;
use App\Domain\Entities\Wallet;
use App\Domain\Exceptions\User\UserDuplicateException;
use App\Domain\UseCases\User\UserStoreUseCase;
use App\Infra\Enums\UserType;
use Carbon\Carbon;
use Faker\Factory;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\TestCase;

#[CoversClass(UserStoreUseCase::class), CoversClass(UserStoreInputDto::class), CoversClass(UserStoreOutputDto::class)]
#[UsesClass(User::class), UsesClass(Wallet::class), UsesClass(UserType::class), UsesClass(UserDuplicateException::class)]
class UserStoreUseCaseTest extends TestCase
{
    private UserStoreUseCase $sut;
    private readonly UuidGeneratorInterface $uuidGeneratorMock;
    private readonly UserRepositoryInterface $userRepoMock;
    private readonly WalletRepositoryInterface $walletRepoMock;
    private readonly User $userStub;

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
            walletId: $faker->uuid(),
            createdAt: new Carbon()
        );

        $walletStub = new Wallet(
            id: $faker->uuid(),
            balance: 0,
            lastBalance: 0,
            createdAt: new Carbon()
        );

        $this->uuidGeneratorMock = $this->createMock(UuidGeneratorInterface::class);
        $this->uuidGeneratorMock->method('generate')->willReturn($faker->uuid());

        $this->userRepoMock = $this->createMock(UserRepositoryInterface::class);
        $this->userRepoMock->method('store')->willReturn($this->userStub);
        $this->userRepoMock->method('findByEmail')->willReturn(null);
        $this->userRepoMock->method('findByIdentify')->willReturn(null);

        $this->walletRepoMock = $this->createMock(WalletRepositoryInterface::class);
        $this->walletRepoMock->method('store')->willReturn($walletStub);

        $this->sut = new UserStoreUseCase($this->uuidGeneratorMock, $this->userRepoMock, $this->walletRepoMock);
    }

    #[Test]
    public function test_handle_success(): void
    {
        $sutData = $this->sut->handle(
            new UserStoreInputDto(
                name: $this->userStub->name,
                userType: $this->userStub->userType->value,
                email: $this->userStub->email,
                password: $this->userStub->password,
                identify: $this->userStub->identify
            )
        );

        $this->assertIsString($sutData->id);
        $this->assertEquals($this->userStub->name, $sutData->name);
        $this->assertEquals(
            $sutData,
            new UserStoreOutputDto(
                id: $this->userStub->id,
                name: $this->userStub->name,
                userType: $this->userStub->userType,
                email: $this->userStub->email,
                identify: $this->userStub->identify,
                walletId: $this->userStub->walletId,
                createdAt: $this->userStub->createdAt,
                updatedAt: $this->userStub->updatedAt
            )
        );
    }

    /**
     * @throws Exception
     */
    #[Test]
    public function test_it_should_throw_duplicate_user_exception_if_email_exists(): void
    {
        $this->expectException(UserDuplicateException::class);

        $mock = $this->createMock(UserRepositoryInterface::class);
        $mock->method('findByEmail')->willReturn($this->userStub);

        $sut = new UserStoreUseCase($this->uuidGeneratorMock, $mock, $this->walletRepoMock);

        $sut->handle(
            new UserStoreInputDto(
                name: $this->userStub->name,
                userType: $this->userStub->userType->value,
                email: $this->userStub->email,
                password: $this->userStub->password,
                identify: $this->userStub->identify
            )
        );
    }

    /**
     * @throws Exception
     */
    #[Test]
    public function test_it_should_throw_duplicate_user_exception_if_identify_exists(): void
    {
        $this->expectException(UserDuplicateException::class);

        $mock = $this->createMock(UserRepositoryInterface::class);
        $mock->method('findByIdentify')->willReturn($this->userStub);

        $sut = new UserStoreUseCase($this->uuidGeneratorMock, $mock, $this->walletRepoMock);

        $sut->handle(
            new UserStoreInputDto(
                name: $this->userStub->name,
                userType: $this->userStub->userType->value,
                email: $this->userStub->email,
                password: $this->userStub->password,
                identify: $this->userStub->identify
            )
        );
    }
}
