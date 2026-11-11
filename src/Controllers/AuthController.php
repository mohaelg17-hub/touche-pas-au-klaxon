<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Csrf;
use App\Core\Database;
use App\Core\Flash;
use App\Models\EmployeRepository;

/**
 * Connexion et deconnexion des utilisateurs.
 */
final class AuthController extends Controller
{
    /**
     * Affiche le formulaire de connexion.
     *
     * @param array<string, string> $params
     */
    public function showLogin(array $params = []): void
    {
        if (Auth::check()) {
            $this->redirect('/');
        }

        $this->render('auth/login', ['title' => 'Connexion']);
    }

    /**
     * Traite le formulaire de connexion.
     *
     * @param array<string, string> $params
     */
    public function login(array $params = []): void
    {
        if (!Csrf::verify($this->input('csrf_token'))) {
            Flash::add('danger', 'Session expirée, veuillez réessayer.');
            $this->redirect('/login');
        }

        $repository = new EmployeRepository(Database::getConnection());
        $employe = $repository->findByEmail($this->input('email'));
        $password = $this->input('password', false);

        if ($employe === null || !password_verify($password, (string) $employe['mot_de_passe'])) {
            Flash::add('danger', 'Identifiant ou mot de passe incorrect.');
            $this->redirect('/login');
        }

        Auth::login($employe);
        $this->redirect('/');
    }

    /**
     * Deconnecte l'utilisateur.
     *
     * @param array<string, string> $params
     */
    public function logout(array $params = []): void
    {
        if (Csrf::verify($this->input('csrf_token'))) {
            Auth::logout();
            Flash::add('secondary', 'Vous êtes déconnecté.');
        }

        $this->redirect('/');
    }
}