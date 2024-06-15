<?php

declare(strict_types=1);

namespace App\Infra\Repositories\Eloquent;

use App\Domain\Contracts\Repositories\User\UserRepositoryInterface;
use App\Domain\Entities\User;

readonly class UserRepository implements UserRepositoryInterface
{
    public function store(array $data): User
    {
        $user = \App\Infra\Entities\User::create($data);

        return new User(
            $user->id,
            $user->name,
            $user->user_type,
            $user->email,
            $user->password,
            $user->identify,
            $user->wallet_id,
            $user->created_at,
            $user->updated_at,
        );
    }

    public function show(string $id): ?User
    {
        $user = \App\Infra\Entities\User::find($id);

        if ($user === null) {
            return null;
        }

        return new User(
            $user->id,
            $user->name,
            $user->user_type,
            $user->email,
            $user->password,
            $user->identify,
            $user->wallet_id,
            $user->created_at,
            $user->updated_at,
        );
    }

    public function credentials(string $email, string $password): ?User
    {
        $user = \App\Infra\Entities\User::where('email', $email)
            ->where('password', $password)
            ->first();

        if ($user === null) {
            return null;
        }

        return new User(
            $user->id,
            $user->name,
            $user->user_type,
            $user->email,
            $user->password,
            $user->identify,
            $user->wallet_id,
            $user->created_at,
            $user->updated_at,
        );
    }

    public function lists(): array
    {
        return \App\Infra\Entities\User::orderBy('name')
            ->get()
            ->map(function (\App\Infra\Entities\User $user) {
                return new User(
                    $user->id,
                    $user->name,
                    $user->user_type,
                    $user->email,
                    $user->password,
                    $user->identify,
                    $user->wallet_id,
                    $user->created_at,
                    $user->updated_at,
                );
            })
            ->toArray();
    }

    public function findByEmail(string $email): ?User
    {
        /* @var \App\Infra\Entities\User|null $user */
        $user = \App\Infra\Entities\User::where('email', $email)->first();

        if ($user === null) {
            return null;
        }

        return new User(
            $user->id,
            $user->name,
            $user->user_type,
            $user->email,
            $user->password,
            $user->identify,
            $user->wallet_id,
            $user->created_at,
            $user->updated_at,
        );
    }

    public function findByIdentify(string $identify): ?User
    {
        /* @var \App\Infra\Entities\User|null $user */
        $user = \App\Infra\Entities\User::where('identify', $identify)->first();

        if ($user === null) {
            return null;
        }

        return new User(
            $user->id,
            $user->name,
            $user->user_type,
            $user->email,
            $user->password,
            $user->identify,
            $user->wallet_id,
            $user->created_at,
            $user->updated_at,
        );
    }
}
