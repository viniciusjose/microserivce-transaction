<?php

declare(strict_types=1);

use Hyperf\Database\Commands\Migrations\MigrateCommand;
use Hyperf\Contract\ApplicationInterface;
use Hyperf\Contract\ConfigInterface;
use Hyperf\Di\ClassLoader;
use Hyperf\Engine\DefaultOption;
use Swoole\Runtime;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Output\ConsoleOutput;

/**
 * This file is part of Hyperf.
 *
 * @see     https://www.hyperf.io
 * @document https://hyperf.wiki
 * @contact  group@hyperf.io
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */
ini_set('display_errors', 'on');
ini_set('display_startup_errors', 'on');

error_reporting(E_ALL);
date_default_timezone_set('America/Sao_Paulo');

Runtime::enableCoroutine(true);

!defined('BASE_PATH') && define('BASE_PATH', dirname(__DIR__, 1));

require BASE_PATH . '/vendor/autoload.php';

!defined('SWOOLE_HOOK_FLAGS') && define('SWOOLE_HOOK_FLAGS', DefaultOption::hookFlags());

ClassLoader::init();

$container = require BASE_PATH . '/config/container.php';

$config = $container->get(ConfigInterface::class);
$config->set('databases.default', $config->get('databases.testing'));

$container->get(ApplicationInterface::class);

\Hyperf\Coroutine\run(function () use ($container) {
    $migrator = $container->get(MigrateCommand::class);
    $migrator->run(new StringInput(''), new ConsoleOutput());
});