<?php

declare(strict_types=1);

use App\Controllers\TrajetController;
use App\Core\Router;

/**
 * Declare toutes les routes de l'application.
 */
return static function (Router $router): void {
    $router->get('/', [TrajetController::class, 'index']);
};