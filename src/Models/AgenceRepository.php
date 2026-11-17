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

    /**
     * Retourne une agence ou null si elle n'existe pas.
     *
     * @return array{id_agence: int, nom: string}|null
     */
    public function findById(int $id): ?array
    {
        $stmt = $this->pdo->prepare('SELECT id_agence, nom FROM agence WHERE id_agence = :id');
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row === false) {
            return null;
        }

        return [
            'id_agence' => (int) $row['id_agence'],
            'nom'       => (string) $row['nom'],
        ];
    }

    /**
     * Cree une agence et retourne son identifiant.
     */
    public function create(string $nom): int
    {
        $stmt = $this->pdo->prepare('INSERT INTO agence (nom) VALUES (:nom)');
        $stmt->execute(['nom' => $nom]);

        return (int) $this->pdo->lastInsertId();
    }

    /**
     * Renomme une agence.
     *
     * @return bool true si la requete a reussi
     */
    public function update(int $id, string $nom): bool
    {
        $stmt = $this->pdo->prepare('UPDATE agence SET nom = :nom WHERE id_agence = :id');

        return $stmt->execute(['nom' => $nom, 'id' => $id]);
    }

    /**
     * Supprime une agence (la base refuse si un trajet l'utilise encore).
     *
     * @return bool true si une agence a ete supprimee
     */
    public function delete(int $id): bool
    {
        $stmt = $this->pdo->prepare('DELETE FROM agence WHERE id_agence = :id');
        $stmt->execute(['id' => $id]);

        return $stmt->rowCount() > 0;
    }

    /**
     * Indique si une agence porte deja ce nom.
     *
     * @param int|null $exceptId Agence a ignorer (utile lors d'une modification)
     */
    public function existsByName(string $nom, ?int $exceptId = null): bool
    {
        $stmt = $this->pdo->prepare(
            'SELECT COUNT(*) FROM agence WHERE nom = :nom AND id_agence <> :except'
        );
        $stmt->execute(['nom' => $nom, 'except' => $exceptId ?? 0]);

        return (int) $stmt->fetchColumn() > 0;
    }

    /**
     * Compte les trajets qui partent de cette agence ou y arrivent.
     */
    public function countTrajets(int $id): int
    {
        $stmt = $this->pdo->prepare(
            'SELECT COUNT(*) FROM trajet
              WHERE id_agence_depart = :id OR id_agence_arrivee = :id2'
        );
        $stmt->execute(['id' => $id, 'id2' => $id]);

        return (int) $stmt->fetchColumn();
    }
}