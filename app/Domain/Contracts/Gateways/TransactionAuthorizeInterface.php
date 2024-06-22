<?php

namespace App\Domain\Contracts\Gateways;

interface TransactionAuthorizeInterface
{
    public function authorize(): bool;
}