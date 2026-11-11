<?php

declare(strict_types=1);

use App\Controllers\AuthController;
use App\Controllers\TrajetController;
use App\Core\Router;

/**
 * Declare toutes les routes de l'application.
 */
return static function (Router $router): void {
    $router->get('/', [TrajetController::class, 'index']);

    $router->get('/login', [AuthController::class, 'showLogin']);
    $router->post('/login', [AuthController::class, 'login']);
    $router->post('/logout', [AuthController::class, 'logout']);
};