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

namespace HyperfTest\Unit\Domain\UseCases\Auth;

use App\Domain\Contracts\Gateways\JwtInterface;
use App\Domain\Contracts\Repositories\User\UserRepositoryInterface;
use App\Domain\DTO\Auth\Login\AuthLoginInputDto;
use App\Domain\DTO\Auth\Login\AuthLoginOutputDto;
use App\Domain\Entities\User;
use App\Domain\Exceptions\User\UserInvalidCredentialsException;
use App\Domain\UseCases\Auth\AuthLoginUseCase;
use App\Infra\Enums\UserType;
use Carbon\Carbon;
use Faker\Factory;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\TestCase;

#[CoversClass(AuthLoginUseCase::class), CoversClass(AuthLoginInputDto::class), CoversClass(AuthLoginOutputDto::class)]
#[UsesClass(User::class), UsesClass(UserInvalidCredentialsException::class)]
class AuthLoginUseCaseTest extends TestCase
{
    private AuthLoginUseCase $sut;
    private User $userStub;

    protected JwtInterface $jwtMock;

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
        $userRepoMock->method('credentials')->willReturn($this->userStub);

        $this->jwtMock = $this->createMock(JwtInterface::class);
        $this->jwtMock->method('encode')->willReturn('any_token');

        $this->sut = new AuthLoginUseCase($userRepoMock, $this->jwtMock);
    }

    #[Test]
    public function test_it_should_be_return_token_dto(): void
    {
        $sutData = $this->sut->handle(
            new AuthLoginInputDto(
                email: $this->userStub->email,
                password: $this->userStub->password
            )
        );

        $this->assertIsString($sutData->token);
    }

    /**
     * @throws Exception
     */
    #[Test]
    public function test_it_should_throw_invalid_credentials_if_credentials_dont_match(): void
    {
        $this->expectException(UserInvalidCredentialsException::class);
        $this->expectExceptionMessage('Invalid credentials');

        $mock = $this->createMock(UserRepositoryInterface::class);
        $mock->method('credentials')->willReturn(null);

        $sut = new AuthLoginUseCase($mock, $this->jwtMock);

        $sut->handle(
            new AuthLoginInputDto(
                email: 'invalid_email',
                password: 'invalid_password',
            )
        );
    }
}
