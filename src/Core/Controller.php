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

    /**
     * Redirige vers une adresse et arrete le script.
     */
    protected function redirect(string $url): never
    {
        header('Location: ' . $url);
        exit;
    }

    /**
     * Lit un champ de formulaire POST (texte uniquement).
     *
     * @param bool $trim Retire les espaces autour (a desactiver pour les mots de passe)
     */
    protected function input(string $key, bool $trim = true): string
    {
        $value = $_POST[$key] ?? '';

        if (!is_string($value)) {
            return '';
        }

        return $trim ? trim($value) : $value;
    }
}