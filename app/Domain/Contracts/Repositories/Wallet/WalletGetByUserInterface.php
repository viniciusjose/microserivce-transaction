<?php

declare(strict_types=1);

namespace App\Domain\Contracts\Repositories\Wallet;

use App\Domain\Entities\Wallet;

interface WalletGetByUserInterface
{
    public function getByUser(string $userId): ?Wallet;
}
