<?php

use App\Core\View;

/** @var list<array{label: string, count: int, href: string, icon: string}> $cards */
?>
<h1 class="h2 my-4">Tableau de bord</h1>

<div class="row g-4">
    <?php foreach ($cards as $card) : ?>
        <div class="col-md-4">
            <div class="card text-center h-100">
                <div class="card-body">
                    <i class="bi <?= View::e($card['icon']) ?> fs-1 text-primary"></i>
                    <p class="display-6 mb-0"><?= View::e($card['count']) ?></p>
                    <p class="text-muted"><?= View::e($card['label']) ?></p>
                    <a href="<?= View::e($card['href']) ?>" class="btn btn-dark">Consulter</a>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>