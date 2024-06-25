<?php

namespace App\Main\Factories\Application\UseCases\User;

use App\Application\UseCases\User\UserListUseCase;
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