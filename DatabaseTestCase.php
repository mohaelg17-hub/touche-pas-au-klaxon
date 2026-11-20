<?php

declare(strict_types=1);

namespace Tests;

use PDO;
use PDOException;
use PHPUnit\Framework\TestCase;
use RuntimeException;

/**
 * Classe mere des tests qui utilisent la base de donnees.
 *
 * Elle travaille TOUJOURS sur la base "klaxon_test" (jamais sur "klaxon") :
 * la base est creee si besoin, ses tables sont recreees a partir de
 * database/schema.sql, puis videes avant chaque test.
 */
abstract class DatabaseTestCase extends TestCase
{
    /** Nom de la base reservee aux tests. */
    private const TEST_DATABASE = 'klaxon_test';

    /** Connexion partagee par tous les tests du lancement en cours. */
    private static ?PDO $shared = null;

    /** Connexion utilisable dans les tests. */
    protected PDO $pdo;

    /**
     * Vide les tables avant chaque test pour que les tests restent independants.
     */
    protected function setUp(): void
    {
        $this->pdo = self::connection();

        $this->pdo->exec('SET FOREIGN_KEY_CHECKS = 0');
        foreach (['trajet', 'employe', 'agence'] as $table) {
            $this->pdo->exec('TRUNCATE TABLE ' . $table);
        }
        $this->pdo->exec('SET FOREIGN_KEY_CHECKS = 1');
    }

    /**
     * Insere une agence et retourne son identifiant.
     */
    protected function insertAgence(string $nom): int
    {
        $stmt = $this->pdo->prepare('INSERT INTO agence (nom) VALUES (:nom)');
        $stmt->execute(['nom' => $nom]);

        return (int) $this->pdo->lastInsertId();
    }

    /**
     * Insere un employe et retourne son identifiant.
     */
    protected function insertEmploye(string $email, string $role = 'user'): int
    {
        $stmt = $this->pdo->prepare(
            'INSERT INTO employe (nom, prenom, telephone, email, mot_de_passe, role)
             VALUES (\'Dupont\', \'Jean\', \'0612345678\', :email, :hash, :role)'
        );
        $stmt->execute([
            'email' => $email,
            'hash'  => password_hash('secret', PASSWORD_DEFAULT),
            'role'  => $role,
        ]);

        return (int) $this->pdo->lastInsertId();
    }

    /**
     * Construit les donnees d'un trajet valide (demain, de 08:00 a 12:00,
     * 4 places dont 3 disponibles). Les valeurs peuvent etre remplacees.
     *
     * @param array<string, int|string> $overrides Valeurs a remplacer
     *
     * @return array<string, int|string>
     */
    protected function trajetData(int $depart, int $arrivee, int $employe, array $overrides = []): array
    {
        return array_merge([
            'id_agence_depart'  => $depart,
            'id_agence_arrivee' => $arrivee,
            'date_depart'       => date('Y-m-d 08:00:00', strtotime('+1 day')),
            'date_arrivee'      => date('Y-m-d 12:00:00', strtotime('+1 day')),
            'places_total'      => 4,
            'places_dispo'      => 3,
            'id_employe'        => $employe,
        ], $overrides);
    }

    /**
     * Retourne la connexion a la base de test (cree la base et les tables au premier appel).
     *
     * @throws RuntimeException Si la connexion est impossible.
     */
    private static function connection(): PDO
    {
        if (self::$shared !== null) {
            return self::$shared;
        }

        $file = dirname(__DIR__) . '/config/config.php';
        if (!is_file($file)) {
            throw new RuntimeException('Fichier config/config.php introuvable : copiez config.example.php.');
        }

        /** @var array{db: array{host: string, port: int, user: string, password: string, charset: string}} $config */
        $config = require $file;
        $db = $config['db'];

        try {
            $pdo = new PDO(
                sprintf('mysql:host=%s;port=%d;charset=%s', $db['host'], $db['port'], $db['charset']),
                $db['user'],
                $db['password'],
                [
                    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                ]
            );
        } catch (PDOException $e) {
            throw new RuntimeException('Connexion MySQL impossible pour les tests : ' . $e->getMessage(), 0, $e);
        }

        $pdo->exec(
            'CREATE DATABASE IF NOT EXISTS ' . self::TEST_DATABASE
            . ' CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci'
        );
        $pdo->exec('USE ' . self::TEST_DATABASE);
        self::createSchema($pdo);

        return self::$shared = $pdo;
    }

    /**
     * Recree les tables a partir de database/schema.sql, sans jamais toucher
     * a la base "klaxon" : seules les instructions CREATE TABLE sont executees.
     */
    private static function createSchema(PDO $pdo): void
    {
        $sql = (string) file_get_contents(dirname(__DIR__) . '/database/schema.sql');
        $sql = (string) preg_replace('/^\xEF\xBB\xBF/', '', $sql);
        $sql = (string) preg_replace('/^\s*--.*$/m', '', $sql);

        $pdo->exec('SET FOREIGN_KEY_CHECKS = 0');
        $pdo->exec('DROP TABLE IF EXISTS trajet, employe, agence');
        $pdo->exec('SET FOREIGN_KEY_CHECKS = 1');

        foreach (explode(';', $sql) as $statement) {
            $statement = trim($statement);

            // Liste blanche : DROP DATABASE, CREATE DATABASE et USE sont ignores.
            if (preg_match('/^CREATE\s+TABLE\b/i', $statement) === 1) {
                $pdo->exec($statement);
            }
        }
    }
}
