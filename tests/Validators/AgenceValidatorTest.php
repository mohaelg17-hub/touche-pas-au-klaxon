<?php

declare(strict_types=1);

namespace Tests\Validators;

use App\Validators\AgenceValidator;
use PHPUnit\Framework\TestCase;

/**
 * Teste les regles de validation du nom d'une agence.
 */
final class AgenceValidatorTest extends TestCase
{
    public function testValidNameProducesNoError(): void
    {
        $this->assertSame([], (new AgenceValidator())->validate('Dijon', false));
    }

    public function testEmptyNameIsRejected(): void
    {
        $this->assertArrayHasKey('nom', (new AgenceValidator())->validate('', false));
    }

    public function testTooLongNameIsRejected(): void
    {
        $nom = str_repeat('a', AgenceValidator::MAX_LENGTH + 1);

        $this->assertArrayHasKey('nom', (new AgenceValidator())->validate($nom, false));
    }

    public function testNameOfMaximumLengthIsAccepted(): void
    {
        $nom = str_repeat('a', AgenceValidator::MAX_LENGTH);

        $this->assertSame([], (new AgenceValidator())->validate($nom, false));
    }

    public function testExistingNameIsRejected(): void
    {
        $this->assertArrayHasKey('nom', (new AgenceValidator())->validate('Paris', true));
    }
}
