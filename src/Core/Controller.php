<?php

declare(strict_types=1);

namespace App\Core;

/**
 * Classe mere de tous les controleurs de l'application.
 */
abstract class Controller
{
    /**
     * Affiche une vue.
     *
     * @param array<string, mixed> $data
     */
    protected function render(string $view, array $data = []): void
    {
        View::render($view, $data);
    }
}