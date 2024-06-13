<?php

namespace App\Main\Factories\Domain\UseCases\User;

use App\Domain\UseCases\User\UserListUseCase;
use App\Main\Factories\Infra\Repositories\Eloquent\UserRepositoryFactory;

class UserListUseCaseFactory
{
    public static function make(): UserListUseCase
    {
        return new UserListUseCase(
            UserRepositoryFactory::make()
        );
    }
}