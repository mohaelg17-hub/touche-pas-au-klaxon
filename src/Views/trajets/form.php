<?php

use App\Core\Csrf;
use App\Core\View;
use App\Validators\TrajetValidator;

/** @var string $title */
/** @var string $action */
/** @var string $submitLabel */
/** @var list<array{id_agence: int, nom: string}> $agences */
/** @var array<string, mixed> $employe */
/** @var array<string, string> $old */
/** @var array<string, string> $errors */

// Petites fonctions d'aide pour ne pas repeter le meme code a chaque champ.
$value = static fn (string $name): string => View::e($old[$name] ?? '');
$invalid = static fn (string $name): string => isset($errors[$name]) ? ' is-invalid' : '';
$feedback = static fn (string $name): string => isset($errors[$name])
    ? '<div class="invalid-feedback">' . View::e($errors[$name]) . '</div>'
    : '';

$identity = [
    'Nom'           => (string) $employe['nom'],
    'Prénom'        => (string) $employe['prenom'],
    'Adresse email' => (string) $employe['email'],
    'Téléphone'     => View::phone((string) $employe['telephone']),
];
?>
<h1 class="h2 my-4"><?= View::e($title) ?></h1>

<form method="post" action="<?= View::e($action) ?>" novalidate>
    <input type="hidden" name="csrf_token" value="<?= View::e(Csrf::token()) ?>">

    <fieldset class="mb-4">
        <legend class="h5">Conducteur</legend>
        <div class="row g-3">
            <?php foreach ($identity as $label => $text) : ?>
                <div class="col-md-6">
                    <label class="form-label w-100"><?= View::e($label) ?>
                        <input type="text" class="form-control" value="<?= View::e($text) ?>" disabled>
                    </label>
                </div>
            <?php endforeach; ?>
        </div>
    </fieldset>

    <fieldset class="mb-4">
        <legend class="h5">Trajet</legend>
        <div class="row g-4">
            <?php foreach (['depart' => 'Départ', 'arrivee' => 'Arrivée'] as $key => $label) : ?>
                <div class="col-md-6">
                    <h2 class="h6"><?= View::e($label) ?></h2>

                    <div class="mb-3">
                        <label for="id_agence_<?= $key ?>" class="form-label">Agence</label>
                        <select class="form-select<?= $invalid('id_agence_' . $key) ?>"
                                id="id_agence_<?= $key ?>" name="id_agence_<?= $key ?>" required>
                            <option value="">Choisir une agence</option>
                            <?php foreach ($agences as $agence) : ?>
                                <option value="<?= $agence['id_agence'] ?>"
                                    <?= (string) $agence['id_agence'] === ($old['id_agence_' . $key] ?? '') ? 'selected' : '' ?>>
                                    <?= View::e($agence['nom']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <?= $feedback('id_agence_' . $key) ?>
                    </div>

                    <div class="mb-3">
                        <label for="date_<?= $key ?>" class="form-label">Date et heure</label>
                        <input type="datetime-local" class="form-control<?= $invalid('date_' . $key) ?>"
                               id="date_<?= $key ?>" name="date_<?= $key ?>"
                               value="<?= $value('date_' . $key) ?>" required>
                        <?= $feedback('date_' . $key) ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </fieldset>

    <fieldset class="mb-4">
        <legend class="h5">Places</legend>
        <div class="row g-3">
            <div class="col-md-6">
                <label for="places_total" class="form-label">Nombre total de places</label>
                <input type="number" class="form-control<?= $invalid('places_total') ?>"
                       id="places_total" name="places_total" min="1" max="<?= TrajetValidator::MAX_PLACES ?>"
                       value="<?= $value('places_total') ?>" required>
                <?= $feedback('places_total') ?>
            </div>
            <div class="col-md-6">
                <label for="places_dispo" class="form-label">Nombre de places disponibles</label>
                <input type="number" class="form-control<?= $invalid('places_dispo') ?>"
                       id="places_dispo" name="places_dispo" min="0" max="<?= TrajetValidator::MAX_PLACES ?>"
                       value="<?= $value('places_dispo') ?>" required>
                <?= $feedback('places_dispo') ?>
            </div>
        </div>
    </fieldset>

    <button type="submit" class="btn btn-dark"><?= View::e($submitLabel) ?></button>
    <a href="/" class="btn btn-secondary">Annuler</a>
</form>