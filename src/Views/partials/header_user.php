<?php

use App\Core\Auth;
use App\Core\Csrf;
use App\Core\View;

$user = Auth::user();
?>
<header class="container py-3">
    <nav class="navbar bg-light border rounded px-3">
        <a class="navbar-brand" href="/">Touche pas au klaxon</a>
        <div class="d-flex align-items-center gap-3">
            <a class="btn btn-dark" href="/trajets/create">Créer un trajet</a>
            <span>Bonjour <?= View::e($user['prenom'] ?? '') ?> <?= View::e($user['nom'] ?? '') ?></span>
            <form method="post" action="/logout" class="m-0">
                <input type="hidden" name="csrf_token" value="<?= View::e(Csrf::token()) ?>">
                <button type="submit" class="btn btn-dark">Déconnexion</button>
            </form>
        </div>
    </nav>
</header>