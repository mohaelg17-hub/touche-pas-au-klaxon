<?php

use App\Core\Flash;
use App\Core\View;

foreach (Flash::consume() as $message) : ?>
    <div class="alert alert-<?= View::e($message['type']) ?>" role="alert">
        <?= View::e($message['message']) ?>
    </div>
<?php endforeach; ?>