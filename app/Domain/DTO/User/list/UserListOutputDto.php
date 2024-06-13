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

namespace App\Domain\DTO\User\list;

use App\Domain\DTO\User\show\UserShowOutputDto;
use App\Domain\Entities\User;
use Hyperf\Contract\Arrayable;

readonly class UserListOutputDto implements Arrayable, \Countable
{
    /**
     * @param UserShowOutputDto[] $users
     */
    public function __construct(
        public array $users
    ) {
    }

    public function toArray(): array
    {
        return [
            'data' => array_map(static fn (UserShowOutputDto $user) => $user->toArray(), $this->users),
        ];
    }

    public function count(): int
    {
        return count($this->users);
    }
}
