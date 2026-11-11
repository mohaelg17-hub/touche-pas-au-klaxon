<?php

declare(strict_types=1);

namespace App\Core;

/**
 * Gere l'utilisateur connecte (stocke en session).
 */
final class Auth
{
    /**
     * Connecte un utilisateur a partir d'une ligne de la table employe.
     *
     * @param array<string, mixed> $employe
     */
    public static function login(array $employe): void
    {
        Session::regenerate();
        Session::set('user', [
            'id'     => (int) $employe['id_employe'],
            'prenom' => (string) $employe['prenom'],
            'nom'    => (string) $employe['nom'],
            'role'   => (string) $employe['role'],
        ]);
    }

    /**
     * Deconnecte l'utilisateur courant.
     */
    public static function logout(): void
    {
        Session::remove('user');
        Session::regenerate();
    }

    /**
     * Retourne l'utilisateur connecte, ou null s'il n'y en a pas.
     *
     * @return array{id: int, prenom: string, nom: string, role: string}|null
     */
    public static function user(): ?array
    {
        $user = Session::get('user');

        if (!is_array($user)) {
            return null;
        }

        /** @var array{id: int, prenom: string, nom: string, role: string} $user */
        return $user;
    }

    /**
     * Indique si quelqu'un est connecte.
     */
    public static function check(): bool
    {
        return self::user() !== null;
    }

    /**
     * Retourne l'identifiant de l'utilisateur connecte.
     */
    public static function id(): ?int
    {
        return self::user()['id'] ?? null;
    }

    /**
     * Indique si l'utilisateur connecte est administrateur.
     */
    public static function isAdmin(): bool
    {
        return (self::user()['role'] ?? '') === 'admin';
    }
}