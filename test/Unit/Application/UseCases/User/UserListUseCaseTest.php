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

namespace HyperfTest\Unit\Application\UseCases\User;

use App\Application\UseCases\User\UserListUseCase;
use App\Domain\Contracts\Repositories\User\UserRepositoryInterface;
use App\Domain\DTO\User\list\UserListOutputDto;
use App\Domain\DTO\User\show\UserShowOutputDto;
use App\Domain\Entities\User;
use App\Domain\Enums\UserType;
use Carbon\Carbon;
use Faker\Factory;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\TestCase;

#[CoversClass(UserListUseCase::class), CoversClass(UserListOutputDto::class), CoversClass(UserShowOutputDto::class)]
class UserListUseCaseTest extends TestCase
{
    private UserListUseCase $sut;

    /**
     * @throws Exception
     */
    protected function setUp(): void
    {
        parent::setUp();

        $faker = Factory::create();

        $userStub = [
            0 => new User(
                id: $faker->uuid(),
                name: $faker->name(),
                userType: UserType::USER,
                email: $faker->valid()->email(),
                password: $faker->password(),
                identify: '123456789',
                createdAt: new Carbon()
            )
        ];

        $userRepoMock = $this->createMock(UserRepositoryInterface::class);
        $userRepoMock->method('lists')->willReturn($userStub);

        $this->sut = new UserListUseCase($userRepoMock);
    }

    #[Test]
    public function test_it_should_return_user(): void
    {
        $sutData = $this->sut->handle();

        self::assertCount(1, $sutData);
        self::assertInstanceOf(UserShowOutputDto::class, $sutData->users[0]);
    }
}
