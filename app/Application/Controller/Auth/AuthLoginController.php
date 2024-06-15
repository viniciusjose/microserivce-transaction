<?php

namespace App\Application\Controller\Auth;

use App\Application\Request\Auth\AuthLoginRequest;
use App\Domain\DTO\Auth\Login\AuthLoginInputDto;
use App\Domain\Exceptions\User\UserInvalidCredentialsException;
use App\Domain\UseCases\Auth\AuthLoginUseCase;
use App\Main\Factories\Domain\UseCases\Auth\AuthLoginUseCaseFactory;
use Hyperf\HttpServer\Contract\ResponseInterface as HttpResponse;
use Psr\Http\Message\ResponseInterface;

class AuthLoginController
{

    private AuthLoginUseCase $useCase;

    public function __construct()
    {
        $this->useCase = AuthLoginUseCaseFactory::make();
    }

    public function __invoke(AuthLoginRequest $request, HttpResponse $response): ResponseInterface
    {
        $httpRequest = $request->validated();
        try {
            $token = $this->useCase->handle(
                new AuthLoginInputDto(
                    email: $httpRequest['email'],
                    password: $httpRequest['password']
                )
            );
        } catch (UserInvalidCredentialsException $e) {
            return $response->withStatus($e->getCode())->json(['message' => $e->getMessage()]);
        } catch (\Throwable $e) {
            return $response->withStatus(500)->json(['message' => 'Internal server error']);
        }

        return $response->json(['token' => $token->token]);
    }
}