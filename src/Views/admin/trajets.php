<?php

use App\Core\Csrf;
use App\Core\View;

/** @var list<array<string, mixed>> $trajets */
$now = date('Y-m-d H:i:s');
?>
<h1 class="h2 my-4">Trajets</h1>

<?php if ($trajets === []) : ?>
    <p class="alert alert-secondary">Aucun trajet en base.</p>
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
                <th>Auteur</th>
                <th>Statut</th>
                <th></th>
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
                    <td><?= View::e($trajet['places_dispo']) ?> / <?= View::e($trajet['places_total']) ?></td>
                    <td><?= View::e($trajet['auteur_prenom'] . ' ' . $trajet['auteur_nom']) ?></td>
                    <td>
                        <?php if ((string) $trajet['date_depart'] <= $now) : ?>
                            <span class="badge text-bg-secondary">Passé</span>
                        <?php elseif ((int) $trajet['places_dispo'] === 0) : ?>
                            <span class="badge text-bg-danger">Complet</span>
                        <?php else : ?>
                            <span class="badge text-bg-success">Disponible</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <form method="post" action="/admin/trajets/<?= View::e($trajet['id_trajet']) ?>/delete"
                              class="d-inline" data-confirm="Supprimer ce trajet ?">
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
<?php endif; ?>