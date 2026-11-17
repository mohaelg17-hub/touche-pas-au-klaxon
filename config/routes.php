<?php

declare(strict_types=1);

use App\Controllers\AdminController;
use App\Controllers\AuthController;
use App\Controllers\TrajetController;
use App\Core\Router;

/**
 * Declare toutes les routes de l'application.
 */
return static function (Router $router): void {
    // Public
    $router->get('/', [TrajetController::class, 'index']);

    // Authentification
    $router->get('/login', [AuthController::class, 'showLogin']);
    $router->post('/login', [AuthController::class, 'login']);
    $router->post('/logout', [AuthController::class, 'logout']);

    // Trajets (utilisateur connecte)
    $router->get('/trajets/create', [TrajetController::class, 'create']);
    $router->post('/trajets/create', [TrajetController::class, 'store']);
    $router->get('/trajets/{id}/edit', [TrajetController::class, 'edit']);
    $router->post('/trajets/{id}/edit', [TrajetController::class, 'update']);
    $router->post('/trajets/{id}/delete', [TrajetController::class, 'delete']);

    // Espace administrateur
    $router->get('/admin', [AdminController::class, 'dashboard']);
    $router->get('/admin/users', [AdminController::class, 'users']);
    $router->get('/admin/agences', [AdminController::class, 'agences']);
    $router->get('/admin/agences/create', [AdminController::class, 'agenceCreate']);
    $router->post('/admin/agences/create', [AdminController::class, 'agenceStore']);
    $router->get('/admin/agences/{id}/edit', [AdminController::class, 'agenceEdit']);
    $router->post('/admin/agences/{id}/edit', [AdminController::class, 'agenceUpdate']);
    $router->post('/admin/agences/{id}/delete', [AdminController::class, 'agenceDelete']);
    $router->get('/admin/trajets', [AdminController::class, 'trajets']);
    $router->post('/admin/trajets/{id}/delete', [AdminController::class, 'trajetDelete']);
};