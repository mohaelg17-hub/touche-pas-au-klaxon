<?php

use App\Core\Csrf;
use App\Core\View;

/** @var list<array{id_agence: int, nom: string}> $agences */
?>
<div class="d-flex justify-content-between align-items-center my-4">
    <h1 class="h2 m-0">Agences</h1>
    <a class="btn btn-dark" href="/admin/agences/create">Ajouter une agence</a>
</div>

<table class="table table-striped table-klaxon align-middle">
    <thead>
        <tr>
            <th>Nom</th>
            <th class="text-end">Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($agences as $agence) : ?>
            <tr>
                <td><?= View::e($agence['nom']) ?></td>
                <td class="text-end text-nowrap">
                    <a class="btn btn-link p-0 text-dark" title="Modifier"
                       href="/admin/agences/<?= $agence['id_agence'] ?>/edit">
                        <i class="bi bi-pencil-square"></i>
                    </a>
                    <form method="post" action="/admin/agences/<?= $agence['id_agence'] ?>/delete"
                          class="d-inline"
                          data-confirm="Supprimer l'agence <?= View::e($agence['nom']) ?> ?">
                        <input type="hidden" name="csrf_token" value="<?= View::e(Csrf::token()) ?>">
                        <button type="submit" class="btn btn-link p-0 text-dark" title="Supprimer">
                            <i class="bi bi-trash"></i>
                        </button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>