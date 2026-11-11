<?php

use App\Core\Auth;
use App\Core\View;

$header = Auth::isAdmin() ? 'header_admin' : (Auth::check() ? 'header_user' : 'header_visiteur');
?>
<!doctype html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= View::e($title ?? 'Touche pas au klaxon') ?></title>
    <link rel="stylesheet" href="/assets/css/main.css">
</head>
<body class="d-flex flex-column min-vh-100">
    <?php require __DIR__ . '/../partials/' . $header . '.php'; ?>

    <main class="container flex-grow-1">
        <?php require __DIR__ . '/../partials/flash.php'; ?>
        <?= $content ?>
    </main>

    <footer class="container text-center py-3">
        © <?= date('Y') ?> - CENEF - MVC PHP
    </footer>
</body>
</html>