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

use App\Application\Request\User\UserStoreRequest;
use App\Domain\DTO\User\store\UserStoreInputDto;
use App\Domain\Exceptions\User\UserDuplicateException;
use App\Domain\UseCases\User\UserListUseCase;
use App\Domain\UseCases\User\UserStoreUseCase;
use App\Main\Factories\Domain\UseCases\User\UserListUseCaseFactory;
use App\Main\Factories\Domain\UseCases\User\UserStoreUseCaseFactory;
use Exception;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Contract\ResponseInterface;
use Hyperf\Logger\LoggerFactory;
use Psr\Log\LoggerInterface;

class UserListController
{
    protected LoggerInterface $logger;
    private UserListUseCase $useCase;

    public function __construct(LoggerFactory $logger)
    {
        $this->useCase = UserListUseCaseFactory::make();
        $this->logger = $logger->get();
    }

    public function __invoke(
        ResponseInterface $response
    ): \Psr\Http\Message\ResponseInterface {
        try {
            $users = $this->useCase->handle();
        } catch (\Throwable $e) {
            $this->logger->error($e->getMessage());

            return $response->withStatus(500)->json(['message' => 'Internal server error']);
        }

        return $response->withStatus(200)->json($users);
    }
}
