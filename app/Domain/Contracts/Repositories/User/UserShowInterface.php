<?php

namespace App\Domain\Contracts\Repositories\User;

use App\Domain\Entities\User;

interface UserShowInterface
{
    public function show(string $id): ?User;
}
