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

namespace App\Application\Controller\User;

use App\Application\Request\UserStoreRequest;
use App\Domain\DTO\User\store\UserStoreInputDto;
use App\Domain\Exceptions\User\UserDuplicateException;
use App\Domain\UseCases\User\UserStoreUseCase;
use App\Main\Factories\Domain\UseCases\User\UserStoreUseCaseFactory;
use Exception;
use Hyperf\HttpServer\Contract\ResponseInterface;

class UserStoreController
{
    private UserStoreUseCase $useCase;

    public function __construct()
    {
        $this->useCase = UserStoreUseCaseFactory::make();
    }

    public function __invoke(
        UserStoreRequest $request,
        ResponseInterface $response
    ): \Psr\Http\Message\ResponseInterface {
        $httpRequest = $request->validated();

        try {
            $user = $this->useCase->handle(
                new UserStoreInputDto(
                    $httpRequest['name'],
                    $httpRequest['userType'],
                    $httpRequest['email'],
                    $httpRequest['password'],
                    $httpRequest['identify']
                )
            );
        } catch (UserDuplicateException $e) {
            return $response->withStatus($e->getCode())->json(['message' => $e->getMessage()]);
        } catch (Exception $e) {
            return $response->withStatus(500)->json(['message' => 'Internal Server Error']);
        }

        return $response->json($user);
    }
}
