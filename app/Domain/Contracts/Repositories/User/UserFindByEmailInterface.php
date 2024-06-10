<?php

namespace App\Domain\Contracts\Repositories\User;

use App\Domain\Entities\User;

interface UserFindByEmailInterface
{
    public function findByEmail(string $email): ?User;
}
