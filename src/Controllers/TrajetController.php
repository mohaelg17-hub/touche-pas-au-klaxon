<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;

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
        $this->render('trajets/index', ['title' => 'Trajets proposés']);
    }
}