<?php

namespace App\Main\Factories\Domain\UseCases\Auth;

use App\Domain\UseCases\Auth\AuthLoginUseCase;
use App\Main\Factories\Infra\Gateways\JwtFactory;
use App\Main\Factories\Infra\Repositories\Eloquent\UserRepositoryFactory;

class AuthLoginUseCaseFactory
{
    public static function make(): AuthLoginUseCase
    {
        return new AuthLoginUseCase(
            UserRepositoryFactory::make(),
            JwtFactory::make()
        );
    }
}