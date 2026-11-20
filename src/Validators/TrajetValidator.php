<?php

declare(strict_types=1);

namespace App\Validators;

use DateTimeImmutable;
use InvalidArgumentException;

/**
 * Verifie les donnees saisies dans le formulaire d'un trajet.
 *
 * @phpstan-type TrajetData array{
 *     id_agence_depart: int,
 *     id_agence_arrivee: int,
 *     date_depart: string,
 *     date_arrivee: string,
 *     places_total: int,
 *     places_dispo: int,
 *     id_employe: int
 * }
 */
final class TrajetValidator
{
    /** Nombre maximal de places proposees dans un vehicule. */
    public const MAX_PLACES = 8;

    /** Format envoye par un champ HTML datetime-local. */
    private const DATE_FORMAT = 'Y-m-d\TH:i';

    /**
     * Retourne la liste des erreurs (vide si les donnees sont valides).
     *
     * @param array<string, string> $input     Donnees du formulaire
     * @param array<int, int>       $agenceIds Identifiants d'agences existantes
     *
     * @return array<string, string> Messages d'erreur indexes par nom de champ
     */
    public function validate(array $input, array $agenceIds, ?DateTimeImmutable $now = null): array
    {
        $now ??= new DateTimeImmutable();
        $errors = [];

        $depart = (int) ($input['id_agence_depart'] ?? 0);
        $arrivee = (int) ($input['id_agence_arrivee'] ?? 0);

        if (!in_array($depart, $agenceIds, true)) {
            $errors['id_agence_depart'] = 'Choisissez une agence de départ.';
        }

        if (!in_array($arrivee, $agenceIds, true)) {
            $errors['id_agence_arrivee'] = 'Choisissez une agence d\'arrivée.';
        } elseif ($arrivee === $depart) {
            $errors['id_agence_arrivee'] = 'L\'agence d\'arrivée doit être différente de l\'agence de départ.';
        }

        $dateDepart = self::parseDate($input['date_depart'] ?? '');
        $dateArrivee = self::parseDate($input['date_arrivee'] ?? '');

        if ($dateDepart === null) {
            $errors['date_depart'] = 'Saisissez une date et une heure de départ valides.';
        } elseif ($dateDepart <= $now) {
            $errors['date_depart'] = 'Le départ doit avoir lieu dans le futur.';
        }

        if ($dateArrivee === null) {
            $errors['date_arrivee'] = 'Saisissez une date et une heure d\'arrivée valides.';
        } elseif ($dateDepart !== null && $dateArrivee <= $dateDepart) {
            $errors['date_arrivee'] = 'L\'arrivée doit avoir lieu après le départ.';
        }

        $total = self::parseInt($input['places_total'] ?? '');
        if ($total === null || $total < 1 || $total > self::MAX_PLACES) {
            $errors['places_total'] = sprintf(
                'Le nombre total de places doit être compris entre 1 et %d.',
                self::MAX_PLACES
            );
        }

        $dispo = self::parseInt($input['places_dispo'] ?? '');
        if ($dispo === null) {
            $errors['places_dispo'] = 'Le nombre de places disponibles doit être un nombre entier positif ou nul.';
        } elseif ($total !== null && $dispo > $total) {
            $errors['places_dispo'] = 'Il ne peut pas y avoir plus de places disponibles que de places au total.';
        }

        return $errors;
    }

    /**
     * Convertit des donnees valides en tableau pret pour l'insertion en base.
     * A appeler uniquement apres validate().
     *
     * @param array<string, string> $input
     *
     * @return TrajetData
     *
     * @throws InvalidArgumentException Si les dates sont invalides.
     */
    public function toData(array $input, int $employeId): array
    {
        $depart = self::parseDate($input['date_depart'] ?? '');
        $arrivee = self::parseDate($input['date_arrivee'] ?? '');

        if ($depart === null || $arrivee === null) {
            throw new InvalidArgumentException('Donnees invalides : appelez validate() avant toData().');
        }

        return [
            'id_agence_depart'  => (int) ($input['id_agence_depart'] ?? 0),
            'id_agence_arrivee' => (int) ($input['id_agence_arrivee'] ?? 0),
            'date_depart'       => $depart->format('Y-m-d H:i:s'),
            'date_arrivee'      => $arrivee->format('Y-m-d H:i:s'),
            'places_total'      => (int) ($input['places_total'] ?? 0),
            'places_dispo'      => (int) ($input['places_dispo'] ?? 0),
            'id_employe'        => $employeId,
        ];
    }

    /**
     * Lit une date au format datetime-local, ou retourne null si elle est invalide.
     */
    private static function parseDate(string $value): ?DateTimeImmutable
    {
        $date = DateTimeImmutable::createFromFormat('!' . self::DATE_FORMAT, $value);

        return $date !== false && $date->format(self::DATE_FORMAT) === $value ? $date : null;
    }

    /**
     * Lit un entier positif ou nul, ou retourne null si la valeur n'en est pas un.
     */
    private static function parseInt(string $value): ?int
    {
        return ctype_digit($value) ? (int) $value : null;
    }
}