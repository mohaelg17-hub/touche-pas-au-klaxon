<?php

declare(strict_types=1);

namespace Tests\Models;

use App\Models\EmployeRepository;
use Tests\DatabaseTestCase;

/**
 * Teste les lectures de EmployeRepository sur la base de test.
 */
final class EmployeRepositoryTest extends DatabaseTestCase
{
    public function testFindByEmailReturnsTheEmployeeWithItsPasswordHash(): void
    {
        $this->insertEmploye('jean@test.fr');

        $employe = (new EmployeRepository($this->pdo))->findByEmail('jean@test.fr');

        $this->assertNotNull($employe);
        $this->assertTrue(password_verify('secret', (string) $employe['mot_de_passe']));
    }

    public function testFindByEmailReturnsNullForAnUnknownAddress(): void
    {
        $this->assertNull((new EmployeRepository($this->pdo))->findByEmail('inconnu@test.fr'));
    }

    public function testFindByIdAndFindAllNeverExposeThePasswordHash(): void
    {
        $id = $this->insertEmploye('jean@test.fr', 'admin');
        $repository = new EmployeRepository($this->pdo);

        $one = $repository->findById($id);
        $all = $repository->findAll();

        $this->assertNotNull($one);
        $this->assertSame('admin', $one['role']);
        $this->assertArrayNotHasKey('mot_de_passe', $one);
        $this->assertCount(1, $all);
        $this->assertArrayNotHasKey('mot_de_passe', $all[0]);
    }
}
