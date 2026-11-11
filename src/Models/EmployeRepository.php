<?php

declare(strict_types=1);

namespace App\Models;

use PDO;

/**
 * Acces aux donnees de la table "employe" (lecture seule).
 */
final class EmployeRepository
{
    public function __construct(private readonly PDO $pdo)
    {
    }

    /**
     * Retourne un employe a partir de son adresse email, ou null s'il n'existe pas.
     *
     * @return array<string, mixed>|null
     */
    public function findByEmail(string $email): ?array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM employe WHERE email = :email');
        $stmt->execute(['email' => $email]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        /** @var array<string, mixed>|false $row */
        return $row === false ? null : $row;
    }
}