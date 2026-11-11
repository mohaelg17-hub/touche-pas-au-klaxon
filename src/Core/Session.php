<?php

declare(strict_types=1);

namespace App\Core;

/**
 * Acces simplifie a la session PHP.
 */
final class Session
{
    /**
     * Demarre la session si elle n'est pas deja active.
     */
    public static function start(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_set_cookie_params(['httponly' => true, 'samesite' => 'Lax']);
            session_start();
        }
    }

    /**
     * Lit une valeur de session.
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        return $_SESSION[$key] ?? $default;
    }

    /**
     * Ecrit une valeur en session.
     */
    public static function set(string $key, mixed $value): void
    {
        $_SESSION[$key] = $value;
    }

    /**
     * Supprime une valeur de session.
     */
    public static function remove(string $key): void
    {
        unset($_SESSION[$key]);
    }

    /**
     * Change l'identifiant de session (protection contre le vol de session).
     */
    public static function regenerate(): void
    {
        session_regenerate_id(true);
    }
}