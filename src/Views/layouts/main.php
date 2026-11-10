<?php use App\Core\View; ?>
<!doctype html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= View::e($title ?? 'Touche pas au klaxon') ?></title>
    <link rel="stylesheet" href="/assets/css/main.css">
</head>
<body class="d-flex flex-column min-vh-100">
    <header class="container py-3">
        <nav class="navbar bg-light border rounded px-3">
            <a class="navbar-brand" href="/">Touche pas au klaxon</a>
        </nav>
    </header>

    <main class="container flex-grow-1">
        <?= $content ?>
    </main>

    <footer class="container text-center py-3">
        © <?= date('Y') ?> - CENEF - MVC PHP
    </footer>
</body>
</html>