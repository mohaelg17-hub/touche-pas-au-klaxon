<?php

declare(strict_types=1);

namespace App\Models;

use PDO;

/**
 * Acces aux donnees de la table "agence".
 */
final class AgenceRepository
{
    public function __construct(private readonly PDO $pdo)
    {
    }

    /**
     * Retourne toutes les agences, triees par nom.
     *
     * @return list<array{id_agence: int, nom: string}>
     */
    public function findAll(): array
    {
        $stmt = $this->pdo->query('SELECT id_agence, nom FROM agence ORDER BY nom');

        if ($stmt === false) {
            return [];
        }

        $agences = [];
        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $agences[] = [
                'id_agence' => (int) $row['id_agence'],
                'nom'       => (string) $row['nom'],
            ];
        }

        return $agences;
    }
}