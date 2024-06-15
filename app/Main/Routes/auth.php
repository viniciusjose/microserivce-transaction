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

use App\Application\Controller\Auth\AuthLoginController;
use Hyperf\HttpServer\Router\Router;

Router::addGroup(
    'auth/',
    static function () {
        Router::post('login', [AuthLoginController::class, '__invoke']);
    }
);
