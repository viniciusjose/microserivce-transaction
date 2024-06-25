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

namespace App\Application\UseCases\User;

use App\Domain\Contracts\Repositories\User\UserListInterface;
use App\Domain\DTO\User\list\UserListOutputDto;
use App\Domain\DTO\User\show\UserShowOutputDto;

readonly class UserListUseCase
{
    public function __construct(
        private UserListInterface $userRepository,
    ) {
    }

    public function handle(): UserListOutputDto
    {
        $users = $this->userRepository->lists();

        $users = array_map(
            static fn($user) => new UserShowOutputDto(
                $user->id,
                $user->name,
                $user->userType,
                $user->email,
                $user->identify,
                $user->createdAt,
                $user->updatedAt
            ),
            $users
        );

        return new UserListOutputDto($users);
    }
}
