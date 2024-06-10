<?php

namespace App\Domain\Contracts\Repositories\User;

use App\Domain\Entities\User;

interface UserStoreInterface
{
    public function store(array $data): User;
}
