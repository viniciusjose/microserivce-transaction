<?php

declare(strict_types=1);

namespace App\Domain\Entities;

use App\Domain\Exceptions\Transaction\InvalidValueException;
use Carbon\Carbon;
use Decimal\Decimal;

readonly class Transaction
{
    public function __construct(
        public string $payerWalletId,
        public string $payeeWalletId,
        public Decimal $value,
        public Carbon $date,
        public ?string $id = null
    ) {
    }

    public function checkValue(): Transaction
    {
        if ($this->value <= 0) {
            throw new InvalidValueException(
                'The value must be greater than 0',
                400
            );
        }

        return $this;
    }
}
