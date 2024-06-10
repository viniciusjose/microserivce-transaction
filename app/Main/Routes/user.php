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
use App\Application\Controller\User\UserStoreController;
use Hyperf\HttpServer\Router\Router;

Router::addGroup('/user', function () {
    Router::post('', [UserStoreController::class, '__invoke']);
});