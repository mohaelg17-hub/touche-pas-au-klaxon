<?php

declare(strict_types=1);

namespace App\Models;

use PDO;

/**
 * Acces aux donnees de la table "trajet".
 */
final class TrajetRepository
{
    public function __construct(private readonly PDO $pdo)
    {
    }

    /**
     * Retourne les trajets a venir qui ont encore des places,
     * tries par date de depart croissante.
     *
     * @return list<array<string, mixed>>
     */
    public function findAvailable(): array
    {
        $sql = 'SELECT t.id_trajet,
                       ad.nom AS agence_depart,
                       aa.nom AS agence_arrivee,
                       t.date_depart,
                       t.date_arrivee,
                       t.places_dispo
                FROM trajet t
                INNER JOIN agence ad ON ad.id_agence = t.id_agence_depart
                INNER JOIN agence aa ON aa.id_agence = t.id_agence_arrivee
                WHERE t.places_dispo > 0
                  AND t.date_depart > NOW()
                ORDER BY t.date_depart ASC';

        $stmt = $this->pdo->query($sql);
        $rows = $stmt === false ? [] : $stmt->fetchAll(PDO::FETCH_ASSOC);

        /** @var list<array<string, mixed>> $rows */
        return $rows;
    }
}