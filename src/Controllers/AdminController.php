<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Database;
use App\Core\Flash;
use App\Models\AgenceRepository;
use App\Models\EmployeRepository;
use App\Models\TrajetRepository;
use App\Validators\AgenceValidator;

/**
 * Espace administrateur : tableau de bord, utilisateurs, agences, trajets.
 * Toutes les methodes publiques exigent un administrateur connecte.
 */
final class AdminController extends Controller
{
    /**
     * Tableau de bord avec un compteur par rubrique.
     *
     * @param array<string, string> $params
     */
    public function dashboard(array $params = []): void
    {
        $this->requireAdmin();
        $pdo = Database::getConnection();

        $this->render('admin/dashboard', [
            'title' => 'Tableau de bord',
            'cards' => [
                [
                    'label' => 'Utilisateurs',
                    'count' => count((new EmployeRepository($pdo))->findAll()),
                    'href'  => '/admin/users',
                    'icon'  => 'bi-people',
                ],
                [
                    'label' => 'Agences',
                    'count' => count((new AgenceRepository($pdo))->findAll()),
                    'href'  => '/admin/agences',
                    'icon'  => 'bi-building',
                ],
                [
                    'label' => 'Trajets',
                    'count' => count((new TrajetRepository($pdo))->findAll()),
                    'href'  => '/admin/trajets',
                    'icon'  => 'bi-car-front',
                ],
            ],
        ]);
    }

    /**
     * Liste des utilisateurs (lecture seule).
     *
     * @param array<string, string> $params
     */
    public function users(array $params = []): void
    {
        $this->requireAdmin();

        $this->render('admin/users', [
            'title' => 'Utilisateurs',
            'users' => (new EmployeRepository(Database::getConnection()))->findAll(),
        ]);
    }

    /**
     * Liste des agences.
     *
     * @param array<string, string> $params
     */
    public function agences(array $params = []): void
    {
        $this->requireAdmin();

        $this->render('admin/agences', [
            'title'   => 'Agences',
            'agences' => (new AgenceRepository(Database::getConnection()))->findAll(),
        ]);
    }

    /**
     * Affiche le formulaire de creation d'une agence.
     *
     * @param array<string, string> $params
     */
    public function agenceCreate(array $params = []): void
    {
        $this->requireAdmin();

        $this->showAgenceForm('Créer une agence', '/admin/agences/create', 'Créer l\'agence', '', []);
    }

    /**
     * Traite la creation d'une agence.
     *
     * @param array<string, string> $params
     */
    public function agenceStore(array $params = []): void
    {
        $this->requireAdmin();
        $this->verifyCsrf('/admin/agences/create');

        $nom = $this->input('nom');
        $repository = new AgenceRepository(Database::getConnection());
        $errors = (new AgenceValidator())->validate($nom, $repository->existsByName($nom));

        if ($errors !== []) {
            $this->showAgenceForm('Créer une agence', '/admin/agences/create', 'Créer l\'agence', $nom, $errors);

            return;
        }

        $repository->create($nom);

        Flash::add('secondary', 'L\'agence a été créée');
        $this->redirect('/admin/agences');
    }

    /**
     * Affiche le formulaire de modification d'une agence.
     *
     * @param array<string, string> $params Parametres de l'URL (id)
     */
    public function agenceEdit(array $params = []): void
    {
        $this->requireAdmin();
        $agence = $this->findAgence($params);

        $this->showAgenceForm(
            'Modifier l\'agence',
            '/admin/agences/' . $agence['id_agence'] . '/edit',
            'Enregistrer',
            $agence['nom'],
            []
        );
    }

    /**
     * Traite la modification d'une agence.
     *
     * @param array<string, string> $params Parametres de l'URL (id)
     */
    public function agenceUpdate(array $params = []): void
    {
        $this->requireAdmin();
        $agence = $this->findAgence($params);
        $action = '/admin/agences/' . $agence['id_agence'] . '/edit';
        $this->verifyCsrf($action);

        $nom = $this->input('nom');
        $repository = new AgenceRepository(Database::getConnection());
        $errors = (new AgenceValidator())->validate($nom, $repository->existsByName($nom, $agence['id_agence']));

        if ($errors !== []) {
            $this->showAgenceForm('Modifier l\'agence', $action, 'Enregistrer', $nom, $errors);

            return;
        }

        $repository->update($agence['id_agence'], $nom);

        Flash::add('secondary', 'L\'agence a été modifiée');
        $this->redirect('/admin/agences');
    }

    /**
     * Supprime une agence, sauf si des trajets l'utilisent encore.
     *
     * @param array<string, string> $params Parametres de l'URL (id)
     */
    public function agenceDelete(array $params = []): void
    {
        $this->requireAdmin();
        $agence = $this->findAgence($params);
        $this->verifyCsrf('/admin/agences');

        $repository = new AgenceRepository(Database::getConnection());

        if ($repository->countTrajets($agence['id_agence']) > 0) {
            Flash::add(
                'danger',
                'Impossible de supprimer l\'agence ' . $agence['nom'] . ' : des trajets l\'utilisent encore.'
            );
            $this->redirect('/admin/agences');
        }

        $repository->delete($agence['id_agence']);

        Flash::add('secondary', 'L\'agence a été supprimée');
        $this->redirect('/admin/agences');
    }

    /**
     * Liste de tous les trajets (passes et complets compris).
     *
     * @param array<string, string> $params
     */
    public function trajets(array $params = []): void
    {
        $this->requireAdmin();

        $this->render('admin/trajets', [
            'title'   => 'Trajets',
            'trajets' => (new TrajetRepository(Database::getConnection()))->findAll(),
        ]);
    }

    /**
     * Supprime n'importe quel trajet.
     *
     * @param array<string, string> $params Parametres de l'URL (id)
     */
    public function trajetDelete(array $params = []): void
    {
        $this->requireAdmin();
        $this->verifyCsrf('/admin/trajets');

        $deleted = (new TrajetRepository(Database::getConnection()))->delete((int) ($params['id'] ?? 0));

        if ($deleted) {
            Flash::add('secondary', 'Le trajet a été supprimé');
        } else {
            Flash::add('warning', 'Ce trajet n\'existe plus.');
        }

        $this->redirect('/admin/trajets');
    }

    /**
     * Retourne l'agence demandee ou affiche une page 404.
     *
     * @param array<string, string> $params Parametres de l'URL (id)
     *
     * @return array{id_agence: int, nom: string}
     */
    private function findAgence(array $params): array
    {
        $agence = (new AgenceRepository(Database::getConnection()))->findById((int) ($params['id'] ?? 0));

        if ($agence === null) {
            $this->abort(404);
        }

        return $agence;
    }

    /**
     * Affiche le formulaire d'agence avec la valeur saisie et les erreurs.
     *
     * @param array<string, string> $errors Messages d'erreur par champ
     */
    private function showAgenceForm(
        string $title,
        string $action,
        string $submitLabel,
        string $nom,
        array $errors
    ): void {
        $this->render('admin/agence_form', [
            'title'       => $title,
            'action'      => $action,
            'submitLabel' => $submitLabel,
            'nom'         => $nom,
            'errors'      => $errors,
        ]);
    }
}