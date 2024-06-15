<?php

namespace App\Domain\UseCases\Auth;

use App\Domain\Contracts\Gateways\JwtInterface;
use App\Domain\Contracts\Repositories\User\UserCredentialsInterface;
use App\Domain\DTO\Auth\Login\AuthLoginInputDto;
use App\Domain\DTO\Auth\Login\AuthLoginOutputDto;
use App\Domain\Exceptions\User\UserInvalidCredentialsException;

class AuthLoginUseCase
{
    public function __construct(
        protected UserCredentialsInterface $userRepo,
        protected JwtInterface $jwt
    ) {
    }

    public function handle(AuthLoginInputDto $input): AuthLoginOutputDto
    {
        $user = $this->userRepo->credentials($input->email, $input->password);

        if (!$user) {
            throw new UserInvalidCredentialsException('Invalid credentials', 401);
        }

        $token = $this->jwt->encode([
            'id'       => $user->id,
            'email'    => $user->email,
            'userType' => $user->userType,
        ]);

        return new AuthLoginOutputDto(
            token: $token
        );
    }
}