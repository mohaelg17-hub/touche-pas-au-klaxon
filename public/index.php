<?php

declare(strict_types=1);

require dirname(__DIR__) . '/vendor/autoload.php';

use App\Core\Database;
use App\Core\Router;
use App\Core\View;

$config = require dirname(__DIR__) . '/config/config.php';

Database::configure($config['db']);
View::setBasePath(dirname(__DIR__) . '/src/Views');

$router = new Router();
(require dirname(__DIR__) . '/config/routes.php')($router);

$router->dispatch($_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI']);