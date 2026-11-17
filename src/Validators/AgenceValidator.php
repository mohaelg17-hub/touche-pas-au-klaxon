<?php

declare(strict_types=1);

namespace App\Validators;

/**
 * Verifie les donnees saisies dans le formulaire d'une agence.
 */
final class AgenceValidator
{
    /** Longueur maximale du nom (taille de la colonne en base). */
    public const MAX_LENGTH = 100;

    /**
     * Retourne la liste des erreurs (vide si le nom est valide).
     *
     * @param string $nom           Nom saisi (deja nettoye des espaces)
     * @param bool   $alreadyExists Indique qu'une autre agence porte deja ce nom
     *
     * @return array<string, string> Messages d'erreur indexes par nom de champ
     */
    public function validate(string $nom, bool $alreadyExists): array
    {
        if ($nom === '') {
            return ['nom' => 'Le nom de l\'agence est obligatoire.'];
        }

        if (mb_strlen($nom) > self::MAX_LENGTH) {
            return ['nom' => sprintf('Le nom ne doit pas dépasser %d caractères.', self::MAX_LENGTH)];
        }

        if ($alreadyExists) {
            return ['nom' => 'Cette agence existe déjà.'];
        }

        return [];
    }
}