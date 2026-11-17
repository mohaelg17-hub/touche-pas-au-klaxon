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
use DateTimeImmutable;

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

        $this->showForm($userId, 'Créer un trajet', '/trajets/create', 'Créer le trajet', [], []);
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
            $this->showForm($userId, 'Créer un trajet', '/trajets/create', 'Créer le trajet', $input, $errors);

            return;
        }

        (new TrajetRepository($pdo))->create($validator->toData($input, $userId));

        Flash::add('secondary', 'Le trajet a été créé');
        $this->redirect('/');
    }

    /**
     * Affiche le formulaire de modification d'un trajet (reserve a son auteur).
     *
     * @param array<string, string> $params Parametres de l'URL (id)
     */
    public function edit(array $params = []): void
    {
        $userId = $this->requireLogin();
        $trajet = $this->findOwned($userId, $params);
        $id = (int) $trajet['id_trajet'];

        $this->showForm(
            $userId,
            'Modifier le trajet',
            '/trajets/' . $id . '/edit',
            'Enregistrer les modifications',
            $this->toFormValues($trajet),
            []
        );
    }

    /**
     * Traite le formulaire de modification d'un trajet (reserve a son auteur).
     *
     * @param array<string, string> $params Parametres de l'URL (id)
     */
    public function update(array $params = []): void
    {
        $userId = $this->requireLogin();
        $trajet = $this->findOwned($userId, $params);
        $id = (int) $trajet['id_trajet'];
        $action = '/trajets/' . $id . '/edit';

        if (!Csrf::verify($this->input('csrf_token'))) {
            Flash::add('danger', 'Session expirée, veuillez réessayer.');
            $this->redirect($action);
        }

        $input = $this->inputs(self::FORM_FIELDS);
        $pdo = Database::getConnection();

        $agenceIds = array_column((new AgenceRepository($pdo))->findAll(), 'id_agence');
        $validator = new TrajetValidator();
        $errors = $validator->validate($input, $agenceIds);

        if ($errors !== []) {
            $this->showForm($userId, 'Modifier le trajet', $action, 'Enregistrer les modifications', $input, $errors);

            return;
        }

        (new TrajetRepository($pdo))->update($id, $validator->toData($input, $userId));

        Flash::add('secondary', 'Le trajet a été modifié');
        $this->redirect('/');
    }

    /**
     * Supprime un trajet (reserve a son auteur).
     *
     * @param array<string, string> $params Parametres de l'URL (id)
     */
    public function delete(array $params = []): void
    {
        $userId = $this->requireLogin();
        $trajet = $this->findOwned($userId, $params);

        if (!Csrf::verify($this->input('csrf_token'))) {
            Flash::add('danger', 'Session expirée, veuillez réessayer.');
            $this->redirect('/');
        }

        (new TrajetRepository(Database::getConnection()))->delete((int) $trajet['id_trajet']);

        Flash::add('secondary', 'Le trajet a été supprimé');
        $this->redirect('/');
    }

    /**
     * Retourne le trajet demande s'il existe ET si l'utilisateur en est l'auteur.
     * Sinon, affiche une page 404 (inexistant) ou 403 (interdit) et arrete le script.
     *
     * @param array<string, string> $params Parametres de l'URL (id)
     *
     * @return array<string, mixed>
     */
    private function findOwned(int $userId, array $params): array
    {
        $id = (int) ($params['id'] ?? 0);
        $trajet = (new TrajetRepository(Database::getConnection()))->findById($id);

        if ($trajet === null) {
            $this->abort(404);
        }

        if ((int) $trajet['id_employe'] !== $userId) {
            $this->abort(403);
        }

        return $trajet;
    }

    /**
     * Convertit un trajet de la base en valeurs de formulaire.
     *
     * @param array<string, mixed> $trajet
     *
     * @return array<string, string>
     */
    private function toFormValues(array $trajet): array
    {
        $format = 'Y-m-d\TH:i';

        return [
            'id_agence_depart'  => (string) $trajet['id_agence_depart'],
            'id_agence_arrivee' => (string) $trajet['id_agence_arrivee'],
            'date_depart'       => (new DateTimeImmutable((string) $trajet['date_depart']))->format($format),
            'date_arrivee'      => (new DateTimeImmutable((string) $trajet['date_arrivee']))->format($format),
            'places_total'      => (string) $trajet['places_total'],
            'places_dispo'      => (string) $trajet['places_dispo'],
        ];
    }

    /**
     * Affiche le formulaire de trajet avec les valeurs saisies et les erreurs.
     *
     * @param array<string, string> $old    Valeurs deja saisies
     * @param array<string, string> $errors Messages d'erreur par champ
     */
    private function showForm(
        int $userId,
        string $title,
        string $action,
        string $submitLabel,
        array $old,
        array $errors
    ): void {
        $pdo = Database::getConnection();
        $employe = (new EmployeRepository($pdo))->findById($userId);

        if ($employe === null) {
            Auth::logout();
            $this->redirect('/login');
        }

        $this->render('trajets/form', [
            'title'       => $title,
            'action'      => $action,
            'submitLabel' => $submitLabel,
            'agences'     => (new AgenceRepository($pdo))->findAll(),
            'employe'     => $employe,
            'old'         => $old,
            'errors'      => $errors,
        ]);
    }
}