<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Database;
use App\Models\TrajetRepository;

/**
 * Gere les pages liees aux trajets.
 */
final class TrajetController extends Controller
{
    /**
     * Page d'accueil : liste des trajets disponibles.
     *
     * @param array<string, string> $params Parametres de l'URL
     */
    public function index(array $params = []): void
    {
        $repository = new TrajetRepository(Database::getConnection());

        $this->render('trajets/index', [
            'title'   => 'Trajets proposés',
            'trajets' => $repository->findAvailable(),
        ]);
    }
}