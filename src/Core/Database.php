<?php

declare(strict_types=1);

namespace App\Core;

use PDO;
use RuntimeException;

/**
 * Fournit une connexion PDO unique a la base de donnees.
 *
 * La configuration est injectee via configure(), ce qui permet
 * d'utiliser une autre base (par exemple pour les tests PHPUnit).
 */
final class Database
{
    private static ?PDO $pdo = null;

    /** @var array{host: string, port: int, name: string, user: string, password: string, charset: string}|null */
    private static ?array $config = null;

    /**
     * Definit les parametres de connexion et reinitialise la connexion.
     *
     * @param array{host: string, port: int, name: string, user: string, password: string, charset: string} $config
     */
    public static function configure(array $config): void
    {
        self::$config = $config;
        self::$pdo = null;
    }

    /**
     * Retourne la connexion PDO (creee a la premiere demande).
     *
     * @throws RuntimeException Si configure() n'a pas ete appele.
     */
    public static function getConnection(): PDO
    {
        if (self::$pdo === null) {
            if (self::$config === null) {
                throw new RuntimeException('La base de donnees n\'est pas configuree.');
            }

            $c = self::$config;
            $dsn = sprintf(
                'mysql:host=%s;port=%d;dbname=%s;charset=%s',
                $c['host'],
                $c['port'],
                $c['name'],
                $c['charset']
            );

            self::$pdo = new PDO($dsn, $c['user'], $c['password'], [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ]);
        }

        return self::$pdo;
    }
}