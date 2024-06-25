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

use App\Application\UseCases\User\UserShowUseCase;
use App\Domain\Contracts\Repositories\User\UserRepositoryInterface;
use App\Domain\DTO\User\show\UserShowInputDto;
use App\Domain\DTO\User\show\UserShowOutputDto;
use App\Domain\Entities\User;
use App\Domain\Enums\UserType;
use App\Domain\Exceptions\User\UserNotFoundException;
use Carbon\Carbon;
use Faker\Factory;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\TestCase;

#[CoversClass(UserShowUseCase::class), CoversClass(UserShowInputDto::class), CoversClass(UserShowOutputDto::class)]
#[UsesClass(User::class), UsesClass(UserNotFoundException::class)]
class UserShowUseCaseTest extends TestCase
{
    private UserShowUseCase $sut;
    private User $userStub;

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

        $userRepoMock = $this->createMock(UserRepositoryInterface::class);
        $userRepoMock->method('show')->willReturn($this->userStub);

        $this->sut = new UserShowUseCase($userRepoMock);
    }

    #[Test]
    public function test_it_should_return_user(): void
    {
        $sutData = $this->sut->handle(
            new UserShowInputDto(
                id: $this->userStub->id
            )
        );

        $this->assertIsString($sutData->id);
        $this->assertEquals($sutData->email, $this->userStub->email);
    }

    /**
     * @throws Exception
     */
    #[Test]
    public function test_it_should_throw_not_found_user_exception_if_id_is_invalid(): void
    {
        $this->expectException(UserNotFoundException::class);

        $mock = $this->createMock(UserRepositoryInterface::class);
        $mock->method('show')->willReturn(null);

        $sut = new UserShowUseCase($mock);

        $sut->handle(
            new UserShowInputDto(
                id: $this->userStub->id
            )
        );
    }
}
