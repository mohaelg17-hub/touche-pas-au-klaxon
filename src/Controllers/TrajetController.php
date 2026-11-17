<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Csrf;
use App\Core\Database;
use App\Core\Flash;
use App\Models\AgenceRepository;
use App\Models\EmployeRepository;
use App\Models\TrajetRepository;
use App\Validators\TrajetValidator;

/**
 * Gere les pages liees aux trajets.
 */
final class TrajetController extends Controller
{
    /** Champs du formulaire de trajet. */
    private const FORM_FIELDS = [
        'id_agence_depart',
        'id_agence_arrivee',
        'date_depart',
        'date_arrivee',
        'places_total',
        'places_dispo',
    ];

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
            'trajets' => $repository->findAvailable(Auth::check()),
        ]);
    }

    /**
     * Affiche le formulaire de creation d'un trajet.
     *
     * @param array<string, string> $params Parametres de l'URL
     */
    public function create(array $params = []): void
    {
        $userId = $this->requireLogin();

        $this->showForm($userId, [], []);
    }

    /**
     * Traite le formulaire de creation d'un trajet.
     *
     * @param array<string, string> $params Parametres de l'URL
     */
    public function store(array $params = []): void
    {
        $userId = $this->requireLogin();

        if (!Csrf::verify($this->input('csrf_token'))) {
            Flash::add('danger', 'Session expirée, veuillez réessayer.');
            $this->redirect('/trajets/create');
        }

        $input = $this->inputs(self::FORM_FIELDS);
        $pdo = Database::getConnection();

        $agenceIds = array_column((new AgenceRepository($pdo))->findAll(), 'id_agence');
        $validator = new TrajetValidator();
        $errors = $validator->validate($input, $agenceIds);

        if ($errors !== []) {
            $this->showForm($userId, $input, $errors);

            return;
        }

        (new TrajetRepository($pdo))->create($validator->toData($input, $userId));

        Flash::add('secondary', 'Le trajet a été créé');
        $this->redirect('/');
    }

    /**
     * Affiche le formulaire de trajet avec les valeurs saisies et les erreurs.
     *
     * @param array<string, string> $old    Valeurs deja saisies
     * @param array<string, string> $errors Messages d'erreur par champ
     */
    private function showForm(int $userId, array $old, array $errors): void
    {
        $pdo = Database::getConnection();
        $employe = (new EmployeRepository($pdo))->findById($userId);

        if ($employe === null) {
            Auth::logout();
            $this->redirect('/login');
        }

        $this->render('trajets/form', [
            'title'       => 'Créer un trajet',
            'action'      => '/trajets/create',
            'submitLabel' => 'Créer le trajet',
            'agences'     => (new AgenceRepository($pdo))->findAll(),
            'employe'     => $employe,
            'old'         => $old,
            'errors'      => $errors,
        ]);
    }
}