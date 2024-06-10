<?php

declare(strict_types=1);

namespace App\Domain\Contracts\Repositories\Wallet;

use App\Domain\Entities\Wallet;

interface WalletStoreInterface
{
    public function store(?array $data = null): Wallet;
}
