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

use App\Application\Controller\User\UserListController;
use App\Application\Controller\User\UserShowController;
use App\Application\Controller\User\UserStoreController;
use App\Application\Middleware\AuthMiddleware;
use Hyperf\HttpServer\Router\Router;

Router::post('user', [UserStoreController::class, '__invoke']);

Router::addGroup(
    'user',
    static function () {
        Router::get('', [UserListController::class, '__invoke']);
        Router::get('/{id}', [UserShowController::class, '__invoke']);
    },
    ['middleware' => [AuthMiddleware::class]]
);

