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

use App\Adapters\Controller\Transaction\TransactionStoreController;
use App\Adapters\Middleware\AuthMiddleware;
use Hyperf\HttpServer\Router\Router;

Router::addGroup(
    'transaction',
    static function () {
        Router::post('', [TransactionStoreController::class, '__invoke']);
    },
    ['middleware' => [AuthMiddleware::class]]
);

