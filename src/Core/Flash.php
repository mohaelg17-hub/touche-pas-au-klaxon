<?php

declare(strict_types=1);

namespace App\Core;

/**
 * Messages "flash" : affiches une seule fois puis effaces.
 */
final class Flash
{
    /**
     * Ajoute un message.
     *
     * @param string $type Style Bootstrap : success, danger, warning, secondary...
     */
    public static function add(string $type, string $message): void
    {
        /** @var list<array{type: string, message: string}> $messages */
        $messages = Session::get('flash', []);
        $messages[] = ['type' => $type, 'message' => $message];
        Session::set('flash', $messages);
    }

    /**
     * Retourne tous les messages en attente et les supprime.
     *
     * @return list<array{type: string, message: string}>
     */
    public static function consume(): array
    {
        /** @var list<array{type: string, message: string}> $messages */
        $messages = Session::get('flash', []);
        Session::remove('flash');

        return $messages;
    }
}