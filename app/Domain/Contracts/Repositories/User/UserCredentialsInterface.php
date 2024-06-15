<?php

namespace App\Domain\Contracts\Repositories\User;

use App\Domain\Entities\User;

interface UserCredentialsInterface
{
    public function credentials(string $email, string $password): ?User;
}
