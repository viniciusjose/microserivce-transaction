<?php

namespace App\Domain\Contracts\Repositories\User;

use App\Domain\Entities\User;

interface UserFindByIdentifyInterface
{
    public function findByIdentify(string $identify): ?User;
}
