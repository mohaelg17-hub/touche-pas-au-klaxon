<?php

use App\Core\View;

/** @var list<array<string, mixed>> $trajets */
?>
<h1 class="h2 my-4">
    <?= \App\Core\Auth::check() ? 'Trajets proposés' : 'Pour obtenir plus d\'informations sur un trajet, veuillez vous connecter' ?>
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
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>