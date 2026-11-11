<?php

declare(strict_types=1);

namespace App\Core;

/**
 * Protection CSRF : jeton secret a inclure dans chaque formulaire POST.
 */
final class Csrf
{
    /**
     * Retourne le jeton de la session (le cree au besoin).
     */
    public static function token(): string
    {
        $token = Session::get('csrf_token');

        if (!is_string($token)) {
            $token = bin2hex(random_bytes(32));
            Session::set('csrf_token', $token);
        }

        return $token;
    }

    /**
     * Verifie qu'un jeton recu correspond a celui de la session.
     */
    public static function verify(?string $token): bool
    {
        $expected = Session::get('csrf_token');

        return is_string($expected) && is_string($token) && hash_equals($expected, $token);
    }
}