<?php

namespace App\Domain\Contracts\Repositories\User;

use App\Domain\Entities\User;

interface UserListInterface
{
    /**
     * @return User[]
     */
    public function lists(): array;
}
