<?php

namespace App\Main\Factories\Domain\UseCases\User;

use App\Domain\UseCases\User\UserShowUseCase;
use App\Main\Factories\Infra\Repositories\Eloquent\UserRepositoryFactory;

class UserShowUseCaseFactory
{
    public static function make(): UserShowUseCase
    {
        return new UserShowUseCase(
            UserRepositoryFactory::make()
        );
    }
}