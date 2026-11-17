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
     * Affiche une page d'erreur (vue errors/<code>) et arrete le script.
     *
     * @param int $status Code HTTP (ex. 403, 404)
     */
    protected function abort(int $status): never
    {
        http_response_code($status);
        View::render('errors/' . $status);
        exit;
    }

    /**
     * Exige un utilisateur connecte, sinon redirige vers la page de connexion.
     *
     * @return int Identifiant de l'utilisateur connecte
     */
    protected function requireLogin(): int
    {
        $id = Auth::id();

        if ($id === null) {
            Flash::add('warning', 'Veuillez vous connecter pour accéder à cette page.');
            $this->redirect('/login');
        }

        return $id;
    }

    /**
     * Exige un administrateur : redirige vers la connexion si personne n'est connecte,
     * affiche une page 403 si l'utilisateur n'est pas administrateur.
     *
     * @return int Identifiant de l'administrateur connecte
     */
    protected function requireAdmin(): int
    {
        $id = $this->requireLogin();

        if (!Auth::isAdmin()) {
            $this->abort(403);
        }

        return $id;
    }

    /**
     * Verifie le jeton CSRF du formulaire envoye ; sinon redirige avec un message.
     *
     * @param string $redirectTo Adresse de redirection en cas d'echec
     */
    protected function verifyCsrf(string $redirectTo): void
    {
        if (!Csrf::verify($this->input('csrf_token'))) {
            Flash::add('danger', 'Session expirée, veuillez réessayer.');
            $this->redirect($redirectTo);
        }
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

    /**
     * Lit plusieurs champs de formulaire POST.
     *
     * @param list<string> $keys Noms des champs
     *
     * @return array<string, string>
     */
    protected function inputs(array $keys): array
    {
        $values = [];

        foreach ($keys as $key) {
            $values[$key] = $this->input($key);
        }

        return $values;
    }
}