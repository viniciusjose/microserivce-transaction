<?php

declare(strict_types=1);

use Hyperf\HttpServer\Router\Router;

Router::addGroup('/api/', static function () {
    require_once BASE_PATH . '/app/Main/Routes/user.php';
});