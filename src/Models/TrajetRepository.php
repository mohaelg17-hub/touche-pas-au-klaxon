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
     * Retourne TOUS les trajets (passes et complets compris) avec leurs agences
     * et leur auteur, pour l'espace administrateur.
     *
     * @return list<array<string, mixed>>
     */
    public function findAll(): array
    {
        $stmt = $this->pdo->query(
            'SELECT t.id_trajet,
                    ad.nom AS agence_depart,
                    aa.nom AS agence_arrivee,
                    t.date_depart,
                    t.date_arrivee,
                    t.places_total,
                    t.places_dispo,
                    e.nom AS auteur_nom,
                    e.prenom AS auteur_prenom
             FROM trajet t
             INNER JOIN agence ad ON ad.id_agence = t.id_agence_depart
             INNER JOIN agence aa ON aa.id_agence = t.id_agence_arrivee
             INNER JOIN employe e ON e.id_employe = t.id_employe
             ORDER BY t.date_depart ASC'
        );
        $rows = $stmt === false ? [] : $stmt->fetchAll(PDO::FETCH_ASSOC);

        /** @var list<array<string, mixed>> $rows */
        return $rows;
    }

    /**
     * Retourne un trajet (colonnes brutes de la table) ou null s'il n'existe pas.
     *
     * @return array<string, mixed>|null
     */
    public function findById(int $id): ?array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM trajet WHERE id_trajet = :id');
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        /** @var array<string, mixed>|false $row */
        return $row === false ? null : $row;
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

    /**
     * Modifie un trajet. L'auteur (id_employe) n'est jamais modifie.
     *
     * @param TrajetData $data Donnees deja validees
     *
     * @return bool true si la requete a reussi
     */
    public function update(int $id, array $data): bool
    {
        $stmt = $this->pdo->prepare(
            'UPDATE trajet
                SET id_agence_depart  = :id_agence_depart,
                    id_agence_arrivee = :id_agence_arrivee,
                    date_depart       = :date_depart,
                    date_arrivee      = :date_arrivee,
                    places_total      = :places_total,
                    places_dispo      = :places_dispo
              WHERE id_trajet = :id_trajet'
        );

        return $stmt->execute([
            'id_agence_depart'  => $data['id_agence_depart'],
            'id_agence_arrivee' => $data['id_agence_arrivee'],
            'date_depart'       => $data['date_depart'],
            'date_arrivee'      => $data['date_arrivee'],
            'places_total'      => $data['places_total'],
            'places_dispo'      => $data['places_dispo'],
            'id_trajet'         => $id,
        ]);
    }

    /**
     * Supprime un trajet.
     *
     * @return bool true si un trajet a ete supprime
     */
    public function delete(int $id): bool
    {
        $stmt = $this->pdo->prepare('DELETE FROM trajet WHERE id_trajet = :id');
        $stmt->execute(['id' => $id]);

        return $stmt->rowCount() > 0;
    }
}