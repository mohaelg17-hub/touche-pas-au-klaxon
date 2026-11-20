<?php

declare(strict_types=1);

namespace Tests\Models;

use App\Models\AgenceRepository;
use App\Models\TrajetRepository;
use PDOException;
use Tests\DatabaseTestCase;

/**
 * Teste les operations de AgenceRepository sur la base de test.
 */
final class AgenceRepositoryTest extends DatabaseTestCase
{
    private AgenceRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->repository = new AgenceRepository($this->pdo);
    }

    public function testCreateInsertsTheAgencyAndReturnsItsId(): void
    {
        $id = $this->repository->create('Dijon');

        $this->assertGreaterThan(0, $id);
        $this->assertSame(['id_agence' => $id, 'nom' => 'Dijon'], $this->repository->findById($id));
    }

    public function testUpdateRenamesTheAgency(): void
    {
        $id = $this->repository->create('Dijon');

        $this->assertTrue($this->repository->update($id, 'Dijon Nord'));
        $this->assertSame('Dijon Nord', $this->repository->findById($id)['nom'] ?? null);
    }

    public function testDeleteRemovesAnUnusedAgency(): void
    {
        $id = $this->repository->create('Dijon');

        $this->assertTrue($this->repository->delete($id));
        $this->assertNull($this->repository->findById($id));
    }

    public function testDeleteReturnsFalseForAnUnknownAgency(): void
    {
        $this->assertFalse($this->repository->delete(9999));
    }

    public function testDatabaseRefusesToDeleteAnAgencyUsedByATrip(): void
    {
        $paris = $this->repository->create('Paris');
        $lyon = $this->repository->create('Lyon');
        $auteur = $this->insertEmploye('auteur@test.fr');
        (new TrajetRepository($this->pdo))->create($this->trajetData($paris, $lyon, $auteur));

        $this->expectException(PDOException::class);

        $this->repository->delete($paris);
    }

    public function testDatabaseRefusesTwoAgenciesWithTheSameName(): void
    {
        $this->repository->create('Paris');

        $this->expectException(PDOException::class);

        $this->repository->create('Paris');
    }

    public function testExistsByNameCanIgnoreTheAgencyBeingEdited(): void
    {
        $id = $this->repository->create('Paris');

        $this->assertTrue($this->repository->existsByName('Paris'));
        $this->assertFalse($this->repository->existsByName('Paris', $id));
        $this->assertFalse($this->repository->existsByName('Lyon'));
    }

    public function testCountTrajetsCountsDeparturesAndArrivals(): void
    {
        $paris = $this->repository->create('Paris');
        $lyon = $this->repository->create('Lyon');
        $nice = $this->repository->create('Nice');
        $auteur = $this->insertEmploye('auteur@test.fr');
        $trajets = new TrajetRepository($this->pdo);
        $trajets->create($this->trajetData($paris, $lyon, $auteur));
        $trajets->create($this->trajetData($lyon, $paris, $auteur));

        $this->assertSame(2, $this->repository->countTrajets($paris));
        $this->assertSame(0, $this->repository->countTrajets($nice));
    }

    public function testFindAllSortsAgenciesByName(): void
    {
        $this->repository->create('Rennes');
        $this->repository->create('Bordeaux');

        $noms = array_column($this->repository->findAll(), 'nom');

        $this->assertSame(['Bordeaux', 'Rennes'], $noms);
    }
}
