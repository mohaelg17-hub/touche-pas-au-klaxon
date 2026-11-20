<?php

declare(strict_types=1);

namespace Tests\Models;

use App\Models\TrajetRepository;
use PDOException;
use Tests\DatabaseTestCase;

/**
 * Teste les operations de TrajetRepository sur la base de test.
 */
final class TrajetRepositoryTest extends DatabaseTestCase
{
    private TrajetRepository $repository;
    private int $paris;
    private int $lyon;
    private int $auteur;

    protected function setUp(): void
    {
        parent::setUp();

        $this->repository = new TrajetRepository($this->pdo);
        $this->paris = $this->insertAgence('Paris');
        $this->lyon = $this->insertAgence('Lyon');
        $this->auteur = $this->insertEmploye('auteur@test.fr');
    }

    public function testCreateInsertsTheTrajetAndReturnsItsId(): void
    {
        $id = $this->repository->create($this->trajetData($this->paris, $this->lyon, $this->auteur));

        $this->assertGreaterThan(0, $id);

        $row = $this->repository->findById($id);
        $this->assertNotNull($row);
        $this->assertSame($this->paris, (int) $row['id_agence_depart']);
        $this->assertSame($this->lyon, (int) $row['id_agence_arrivee']);
        $this->assertSame(4, (int) $row['places_total']);
        $this->assertSame(3, (int) $row['places_dispo']);
        $this->assertSame($this->auteur, (int) $row['id_employe']);
    }

    public function testUpdateChangesTheTrajetButKeepsItsAuthor(): void
    {
        $id = $this->repository->create($this->trajetData($this->paris, $this->lyon, $this->auteur));
        $autre = $this->insertEmploye('autre@test.fr');

        $changes = $this->trajetData($this->lyon, $this->paris, $autre, ['places_total' => 6, 'places_dispo' => 1]);

        $this->assertTrue($this->repository->update($id, $changes));

        $row = $this->repository->findById($id);
        $this->assertNotNull($row);
        $this->assertSame($this->lyon, (int) $row['id_agence_depart']);
        $this->assertSame($this->paris, (int) $row['id_agence_arrivee']);
        $this->assertSame(6, (int) $row['places_total']);
        $this->assertSame(1, (int) $row['places_dispo']);
        $this->assertSame($this->auteur, (int) $row['id_employe'], 'L\'auteur ne doit jamais changer.');
    }

    public function testDeleteRemovesTheTrajet(): void
    {
        $id = $this->repository->create($this->trajetData($this->paris, $this->lyon, $this->auteur));

        $this->assertTrue($this->repository->delete($id));
        $this->assertNull($this->repository->findById($id));
    }

    public function testDeleteReturnsFalseForAnUnknownTrajet(): void
    {
        $this->assertFalse($this->repository->delete(9999));
    }

    public function testFindByIdReturnsNullForAnUnknownTrajet(): void
    {
        $this->assertNull($this->repository->findById(9999));
    }

    public function testDatabaseRefusesTwoIdenticalAgencies(): void
    {
        $this->expectException(PDOException::class);

        $this->repository->create($this->trajetData($this->paris, $this->paris, $this->auteur));
    }

    public function testDatabaseRefusesAnArrivalBeforeTheDeparture(): void
    {
        $this->expectException(PDOException::class);

        $this->repository->create($this->trajetData($this->paris, $this->lyon, $this->auteur, [
            'date_arrivee' => date('Y-m-d 06:00:00', strtotime('+1 day')),
        ]));
    }

    public function testDatabaseRefusesMoreAvailableSeatsThanTotalSeats(): void
    {
        $this->expectException(PDOException::class);

        $this->repository->create($this->trajetData($this->paris, $this->lyon, $this->auteur, [
            'places_total' => 2,
            'places_dispo' => 3,
        ]));
    }

    public function testFindAvailableKeepsOnlyFutureTripsWithSeatsSortedByDeparture(): void
    {
        $tard = $this->repository->create($this->trajetData($this->paris, $this->lyon, $this->auteur, [
            'date_depart'  => date('Y-m-d 10:00:00', strtotime('+3 days')),
            'date_arrivee' => date('Y-m-d 14:00:00', strtotime('+3 days')),
        ]));
        $tot = $this->repository->create($this->trajetData($this->lyon, $this->paris, $this->auteur));
        $this->repository->create($this->trajetData($this->paris, $this->lyon, $this->auteur, ['places_dispo' => 0]));
        $this->repository->create($this->trajetData($this->paris, $this->lyon, $this->auteur, [
            'date_depart'  => date('Y-m-d 08:00:00', strtotime('-2 days')),
            'date_arrivee' => date('Y-m-d 12:00:00', strtotime('-2 days')),
        ]));

        $ids = array_map(static fn (array $row): int => (int) $row['id_trajet'], $this->repository->findAvailable());

        $this->assertSame([$tot, $tard], $ids, 'Ni le trajet complet ni le trajet passe ne doivent apparaitre.');
    }

    public function testFindAvailableOnlyExposesContactDetailsWhenAsked(): void
    {
        $this->repository->create($this->trajetData($this->paris, $this->lyon, $this->auteur));

        $public = $this->repository->findAvailable();
        $this->assertArrayNotHasKey('auteur_email', $public[0]);
        $this->assertArrayNotHasKey('auteur_telephone', $public[0]);

        $details = $this->repository->findAvailable(true);
        $this->assertSame('auteur@test.fr', $details[0]['auteur_email']);
        $this->assertSame(4, (int) $details[0]['places_total']);
    }

    public function testFindAllReturnsEveryTripIncludingPastAndFullOnes(): void
    {
        $this->repository->create($this->trajetData($this->paris, $this->lyon, $this->auteur, ['places_dispo' => 0]));
        $this->repository->create($this->trajetData($this->paris, $this->lyon, $this->auteur, [
            'date_depart'  => date('Y-m-d 08:00:00', strtotime('-2 days')),
            'date_arrivee' => date('Y-m-d 12:00:00', strtotime('-2 days')),
        ]));

        $this->assertCount(2, $this->repository->findAll());
    }
}
