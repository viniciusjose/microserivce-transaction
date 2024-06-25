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

namespace App\Adapters\Controller\User;

use App\Adapters\Request\User\UserStoreRequest;
use App\Application\UseCases\User\UserStoreUseCase;
use App\Domain\DTO\User\store\UserStoreInputDto;
use App\Domain\Exceptions\User\UserDuplicateException;
use App\Main\Factories\Domain\UseCases\User\UserStoreUseCaseFactory;
use Hyperf\HttpServer\Contract\ResponseInterface;
use Hyperf\Logger\LoggerFactory;
use Psr\Log\LoggerInterface;

class UserStoreController
{
    protected LoggerInterface $logger;
    private UserStoreUseCase $useCase;

    public function __construct(LoggerFactory $logger)
    {
        $this->useCase = UserStoreUseCaseFactory::make();
        $this->logger = $logger->get();
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
        } catch (\Throwable $e) {
            $this->logger->error($e->getMessage());

            return $response->withStatus(500)->json(['message' => 'Internal server error']);
        }

        return $response->withStatus(201)->json($user);
    }
}
