<?php

use App\Core\Auth;
use App\Core\Csrf;
use App\Core\View;

/** @var list<array<string, mixed>> $trajets */
$connected = Auth::check();
?>
<h1 class="h2 my-4">
    <?= $connected ? 'Trajets proposés' : 'Pour obtenir plus d\'informations sur un trajet, veuillez vous connecter' ?>
</h1>

<?php if ($trajets === []) : ?>
    <p class="alert alert-secondary">Aucun trajet disponible pour le moment.</p>
<?php else : ?>
    <table class="table table-striped table-klaxon text-center align-middle">
        <thead>
            <tr>
                <th>Départ</th>
                <th>Date</th>
                <th>Heure</th>
                <th>Destination</th>
                <th>Date</th>
                <th>Heure</th>
                <th>Places</th>
                <?php if ($connected) : ?>
                    <th></th>
                <?php endif; ?>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($trajets as $trajet) : ?>
                <tr>
                    <td><?= View::e($trajet['agence_depart']) ?></td>
                    <td><?= View::date((string) $trajet['date_depart']) ?></td>
                    <td><?= View::time((string) $trajet['date_depart']) ?></td>
                    <td><?= View::e($trajet['agence_arrivee']) ?></td>
                    <td><?= View::date((string) $trajet['date_arrivee']) ?></td>
                    <td><?= View::time((string) $trajet['date_arrivee']) ?></td>
                    <td><?= View::e($trajet['places_dispo']) ?></td>
                    <?php if ($connected) : ?>
                        <td class="text-nowrap">
                            <button type="button" class="btn btn-link p-0 text-dark" title="Détails"
                                    data-bs-toggle="modal" data-bs-target="#trajetModal"
                                    data-auteur="<?= View::e($trajet['auteur_prenom'] . ' ' . $trajet['auteur_nom']) ?>"
                                    data-telephone="<?= View::e(View::phone((string) $trajet['auteur_telephone'])) ?>"
                                    data-email="<?= View::e($trajet['auteur_email']) ?>"
                                    data-places="<?= View::e($trajet['places_total']) ?>">
                                <i class="bi bi-eye"></i>
                            </button>

                            <?php if ((int) $trajet['id_employe'] === Auth::id()) : ?>
                                <a class="btn btn-link p-0 text-dark" title="Modifier"
                                   href="/trajets/<?= View::e($trajet['id_trajet']) ?>/edit">
                                    <i class="bi bi-pencil-square"></i>
                                </a>
                                <form method="post" action="/trajets/<?= View::e($trajet['id_trajet']) ?>/delete"
                                      class="d-inline" data-confirm="Supprimer ce trajet ?">
                                    <input type="hidden" name="csrf_token" value="<?= View::e(Csrf::token()) ?>">
                                    <button type="submit" class="btn btn-link p-0 text-dark" title="Supprimer">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            <?php endif; ?>
                        </td>
                    <?php endif; ?>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <?php if ($connected) : ?>
        <div class="modal fade" id="trajetModal" tabindex="-1" aria-labelledby="trajetModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h2 class="modal-title fs-5" id="trajetModalLabel">Détails du trajet</h2>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
                    </div>
                    <div class="modal-body">
                        <p>Auteur : <strong data-field="auteur"></strong></p>
                        <p>Téléphone : <strong data-field="telephone"></strong></p>
                        <p>Email : <strong data-field="email"></strong></p>
                        <p class="mb-0">Nombre total de places : <span data-field="places"></span></p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
<?php endif; ?>