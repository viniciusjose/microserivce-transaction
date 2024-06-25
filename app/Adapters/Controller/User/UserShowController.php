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

use App\Application\UseCases\User\UserShowUseCase;
use App\Domain\DTO\User\show\UserShowInputDto;
use App\Domain\Exceptions\User\UserNotFoundException;
use App\Main\Factories\Application\UseCases\User\UserShowUseCaseFactory;
use Hyperf\HttpServer\Contract\ResponseInterface;
use Hyperf\Logger\LoggerFactory;
use Psr\Log\LoggerInterface;

class UserShowController
{
    protected LoggerInterface $logger;
    private UserShowUseCase $useCase;

    public function __construct(LoggerFactory $logger)
    {
        $this->useCase = UserShowUseCaseFactory::make();
        $this->logger = $logger->get();
    }

    public function __invoke(
        string $id,
        ResponseInterface $response
    ): \Psr\Http\Message\ResponseInterface {
        try {
            $user = $this->useCase->handle(
                new UserShowInputDto(
                    id: $id
                )
            );
        } catch (UserNotFoundException $e) {
            return $response->withStatus($e->getCode())->json(['message' => $e->getMessage()]);
        } catch (\Throwable $e) {
            $this->logger->error($e->getMessage());

            return $response->withStatus(500)->json(['message' => 'Internal server error']);
        }

        return $response->withStatus(200)->json($user);
    }
}
