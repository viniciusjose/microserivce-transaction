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

namespace App\Domain\UseCases\User;

use App\Domain\Contracts\Repositories\User\UserShowInterface;
use App\Domain\DTO\User\show\UserShowInputDto;
use App\Domain\DTO\User\show\UserShowOutputDto;
use App\Domain\Exceptions\User\UserNotFoundException;

readonly class UserShowUseCase
{
    public function __construct(
        private UserShowInterface $userRepository,
    ) {
    }

    public function handle(UserShowInputDto $data): UserShowOutputDto
    {
        $user = $this->userRepository->show($data->id);

        if ($user === null) {
            throw new UserNotFoundException('User not found', 404);
        }

        return new UserShowOutputDto(
            id: $user->id,
            name: $user->name,
            userType: $user->userType,
            email: $user->email,
            identify: $user->identify,
            walletId: $user->walletId,
            createdAt: $user->createdAt,
            updatedAt: $user->updatedAt,
        );
    }
}
