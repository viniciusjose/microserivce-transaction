<?php

declare(strict_types=1);
/**
 * This file is part of Hyperf.
 *
 * @link     https://www.hyperf.io
 * @document https://hyperf.wiki
 * @contact  group@hyperf.io
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */

namespace App\Domain\DTO\User\store;

use App\Infra\Enums\UserType;
use Carbon\Carbon;
use Hyperf\Contract\Arrayable;

readonly class UserStoreOutputDto implements Arrayable
{
    public function __construct(
        public string $id,
        public string $name,
        public UserType $userType,
        public string $email,
        public string $identify,
        public string $walletId,
        public Carbon $createdAt,
        public Carbon|null $updatedAt = null
    ) {
    }

    public function toArray(): array
    {
        return [
            'id'         => $this->id,
            'name'       => $this->name,
            'user_type'  => $this->userType->value,
            'email'      => $this->email,
            'identify'   => $this->identify,
            'wallet_id'  => $this->walletId,
            'created_at' => $this->createdAt,
            'updated_at' => $this->updatedAt,
        ];
    }
}
