<?php

use App\Core\Csrf;
use App\Core\View;
?>
<h1 class="h2 my-4">Connexion</h1>

<form method="post" action="/login" class="col-md-6 col-lg-4">
    <input type="hidden" name="csrf_token" value="<?= View::e(Csrf::token()) ?>">

    <div class="mb-3">
        <label for="email" class="form-label">Adresse email</label>
        <input type="email" class="form-control" id="email" name="email" required autofocus>
    </div>

    <div class="mb-3">
        <label for="password" class="form-label">Mot de passe</label>
        <input type="password" class="form-control" id="password" name="password" required>
    </div>

    <button type="submit" class="btn btn-dark">Se connecter</button>
</form>