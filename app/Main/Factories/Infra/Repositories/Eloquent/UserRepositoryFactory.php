<?php

namespace App\Main\Factories\Infra\Repositories\Eloquent;

use App\Infra\Repositories\Eloquent\UserRepository;

class UserRepositoryFactory
{
    public static function make(): UserRepository
    {
        return new UserRepository();
    }
}