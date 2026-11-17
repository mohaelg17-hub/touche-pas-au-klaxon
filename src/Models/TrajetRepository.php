<?php

declare(strict_types=1);

namespace App\Models;

use App\Validators\TrajetValidator;
use PDO;

/**
 * Acces aux donnees de la table "trajet".
 *
 * @phpstan-import-type TrajetData from TrajetValidator
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
     * @param bool $withDetails Ajoute l'auteur, ses coordonnees et le nombre total de places
     *                          (reserve aux utilisateurs connectes).
     *
     * @return list<array<string, mixed>>
     */
    public function findAvailable(bool $withDetails = false): array
    {
        $columns = $withDetails
            ? ', t.places_total, t.id_employe,
               e.nom AS auteur_nom, e.prenom AS auteur_prenom,
               e.telephone AS auteur_telephone, e.email AS auteur_email'
            : '';
        $join = $withDetails
            ? 'INNER JOIN employe e ON e.id_employe = t.id_employe'
            : '';

        $sql = "SELECT t.id_trajet,
                       ad.nom AS agence_depart,
                       aa.nom AS agence_arrivee,
                       t.date_depart,
                       t.date_arrivee,
                       t.places_dispo
                       $columns
                FROM trajet t
                INNER JOIN agence ad ON ad.id_agence = t.id_agence_depart
                INNER JOIN agence aa ON aa.id_agence = t.id_agence_arrivee
                $join
                WHERE t.places_dispo > 0
                  AND t.date_depart > NOW()
                ORDER BY t.date_depart ASC";

        $stmt = $this->pdo->query($sql);
        $rows = $stmt === false ? [] : $stmt->fetchAll(PDO::FETCH_ASSOC);

        /** @var list<array<string, mixed>> $rows */
        return $rows;
    }

    /**
     * Cree un trajet et retourne son identifiant.
     *
     * @param TrajetData $data Donnees deja validees
     */
    public function create(array $data): int
    {
        $stmt = $this->pdo->prepare(
            'INSERT INTO trajet
                (id_agence_depart, id_agence_arrivee, date_depart, date_arrivee,
                 places_total, places_dispo, id_employe)
             VALUES
                (:id_agence_depart, :id_agence_arrivee, :date_depart, :date_arrivee,
                 :places_total, :places_dispo, :id_employe)'
        );
        $stmt->execute($data);

        return (int) $this->pdo->lastInsertId();
    }
}