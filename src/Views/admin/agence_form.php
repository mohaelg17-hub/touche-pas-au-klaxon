<?php

use App\Core\Csrf;
use App\Core\View;
use App\Validators\AgenceValidator;

/** @var string $title */
/** @var string $action */
/** @var string $submitLabel */
/** @var string $nom */
/** @var array<string, string> $errors */
?>
<h1 class="h2 my-4"><?= View::e($title) ?></h1>

<form method="post" action="<?= View::e($action) ?>" class="col-md-6 col-lg-4" novalidate>
    <input type="hidden" name="csrf_token" value="<?= View::e(Csrf::token()) ?>">

    <div class="mb-3">
        <label for="nom" class="form-label">Nom de l'agence</label>
        <input type="text" class="form-control<?= isset($errors['nom']) ? ' is-invalid' : '' ?>"
               id="nom" name="nom" maxlength="<?= AgenceValidator::MAX_LENGTH ?>"
               value="<?= View::e($nom) ?>" required autofocus>
        <?php if (isset($errors['nom'])) : ?>
            <div class="invalid-feedback"><?= View::e($errors['nom']) ?></div>
        <?php endif; ?>
    </div>

    <button type="submit" class="btn btn-dark"><?= View::e($submitLabel) ?></button>
    <a href="/admin/agences" class="btn btn-secondary">Annuler</a>
</form>