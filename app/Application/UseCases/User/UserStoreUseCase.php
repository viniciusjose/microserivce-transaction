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

use App\Domain\Contracts\Gateways\UuidGeneratorInterface;
use App\Domain\Contracts\Repositories\User\UserFindByEmailInterface;
use App\Domain\Contracts\Repositories\User\UserFindByIdentifyInterface;
use App\Domain\Contracts\Repositories\User\UserStoreInterface;
use App\Domain\Contracts\Repositories\Wallet\WalletStoreInterface;
use App\Domain\DTO\User\store\UserStoreInputDto;
use App\Domain\DTO\User\store\UserStoreOutputDto;
use App\Domain\Exceptions\User\UserDuplicateException;

readonly class UserStoreUseCase
{
    public function __construct(
        private UuidGeneratorInterface $uuidGenerator,
        private UserFindByEmailInterface|UserFindByIdentifyInterface|UserStoreInterface $userRepository,
        private WalletStoreInterface $walletRepository
    ) {
    }

    public function handle(UserStoreInputDto $data): UserStoreOutputDto
    {
        $userFind = $this->userRepository->findByEmail($data->email);

        if ($userFind !== null) {
            throw new UserDuplicateException('User already exists with this email', 409);
        }

        $userFind = $this->userRepository->findByIdentify($data->identify);

        if ($userFind !== null) {
            throw new UserDuplicateException('User already exists with this identify', 409);
        }

        $user = $this->userRepository->store([
            'id'        => $this->uuidGenerator->generate(),
            'name'      => $data->name,
            'user_type' => $data->userType,
            'email'     => $data->email,
            'password'  => $data->password,
            'identify'  => $data->identify
        ]);

        $this->walletRepository->store([
            'id'           => $this->uuidGenerator->generate(),
            'user_id'      => $user->id,
            'balance'      => 0,
            'last_balance' => 0,
        ]);

        return new UserStoreOutputDto(
            id: $user->id,
            name: $user->name,
            userType: $user->userType,
            email: $user->email,
            identify: $user->identify,
            createdAt: $user->createdAt,
            updatedAt: $user->updatedAt
        );
    }
}
