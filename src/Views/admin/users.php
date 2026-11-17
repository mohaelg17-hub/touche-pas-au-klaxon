<?php

use App\Core\View;

/** @var list<array<string, mixed>> $users */
?>
<h1 class="h2 my-4">Utilisateurs</h1>

<table class="table table-striped table-klaxon text-center align-middle">
    <thead>
        <tr>
            <th>Nom</th>
            <th>Prénom</th>
            <th>Email</th>
            <th>Téléphone</th>
            <th>Rôle</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($users as $user) : ?>
            <tr>
                <td><?= View::e($user['nom']) ?></td>
                <td><?= View::e($user['prenom']) ?></td>
                <td><?= View::e($user['email']) ?></td>
                <td><?= View::e(View::phone((string) $user['telephone'])) ?></td>
                <td>
                    <?php if ($user['role'] === 'admin') : ?>
                        <span class="badge text-bg-primary">Administrateur</span>
                    <?php else : ?>
                        <span class="badge text-bg-secondary">Employé</span>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>