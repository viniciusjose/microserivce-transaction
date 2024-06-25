<?php

declare(strict_types=1);

namespace App\Domain\Entities;

use App\Domain\Enums\StatusEnum;
use Carbon\Carbon;

class TransactionNotification
{
    public function __construct(
        readonly public string $transaction_id,
        readonly public Carbon $date,
        readonly public ?string $id = null,
        public ?StatusEnum $status = null,
    ) {
    }

    public function setStatus(StatusEnum $status): void
    {
        $this->status = $status;
    }
}
