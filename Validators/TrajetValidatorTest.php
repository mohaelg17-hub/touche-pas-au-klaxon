<?php

declare(strict_types=1);

namespace Tests\Validators;

use App\Validators\TrajetValidator;
use DateTimeImmutable;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

/**
 * Teste les regles de coherence d'un trajet (aucune base de donnees necessaire).
 */
final class TrajetValidatorTest extends TestCase
{
    private TrajetValidator $validator;
    private DateTimeImmutable $now;

    protected function setUp(): void
    {
        $this->validator = new TrajetValidator();
        $this->now = new DateTimeImmutable('2030-01-01 09:00');
    }

    /**
     * Donnees de formulaire valides, dont certaines valeurs peuvent etre remplacees.
     *
     * @param array<string, string> $overrides
     *
     * @return array<string, string>
     */
    private function input(array $overrides = []): array
    {
        return array_merge([
            'id_agence_depart'  => '1',
            'id_agence_arrivee' => '2',
            'date_depart'       => '2030-01-02T08:00',
            'date_arrivee'      => '2030-01-02T12:30',
            'places_total'      => '4',
            'places_dispo'      => '3',
        ], $overrides);
    }

    public function testValidDataProducesNoError(): void
    {
        $this->assertSame([], $this->validator->validate($this->input(), [1, 2, 3], $this->now));
    }

    public function testDepartureAndArrivalAgenciesMustDiffer(): void
    {
        $errors = $this->validator->validate($this->input(['id_agence_arrivee' => '1']), [1, 2], $this->now);

        $this->assertArrayHasKey('id_agence_arrivee', $errors);
    }

    public function testUnknownAgenciesAreRejected(): void
    {
        $errors = $this->validator->validate($this->input(['id_agence_depart' => '99']), [1, 2], $this->now);

        $this->assertArrayHasKey('id_agence_depart', $errors);
    }

    public function testArrivalMustBeAfterDeparture(): void
    {
        $errors = $this->validator->validate($this->input(['date_arrivee' => '2030-01-02T07:00']), [1, 2], $this->now);

        $this->assertArrayHasKey('date_arrivee', $errors);
    }

    public function testArrivalEqualToDepartureIsRejected(): void
    {
        $errors = $this->validator->validate($this->input(['date_arrivee' => '2030-01-02T08:00']), [1, 2], $this->now);

        $this->assertArrayHasKey('date_arrivee', $errors);
    }

    public function testDepartureMustBeInTheFuture(): void
    {
        $errors = $this->validator->validate(
            $this->input(['date_depart' => '2029-12-31T08:00', 'date_arrivee' => '2029-12-31T12:00']),
            [1, 2],
            $this->now
        );

        $this->assertArrayHasKey('date_depart', $errors);
    }

    public function testMalformedDatesAreRejected(): void
    {
        $errors = $this->validator->validate(
            $this->input(['date_depart' => 'demain', 'date_arrivee' => '2030-13-45T99:99']),
            [1, 2],
            $this->now
        );

        $this->assertArrayHasKey('date_depart', $errors);
        $this->assertArrayHasKey('date_arrivee', $errors);
    }

    public function testAvailableSeatsCannotExceedTotalSeats(): void
    {
        $errors = $this->validator->validate($this->input(['places_total' => '2', 'places_dispo' => '3']), [1, 2], $this->now);

        $this->assertArrayHasKey('places_dispo', $errors);
    }

    public function testZeroAvailableSeatsIsAccepted(): void
    {
        $this->assertSame([], $this->validator->validate($this->input(['places_dispo' => '0']), [1, 2], $this->now));
    }

    public function testTotalSeatsMustBeBetweenOneAndTheMaximum(): void
    {
        foreach (['0', '-1', (string) (TrajetValidator::MAX_PLACES + 1), 'abc', ''] as $value) {
            $errors = $this->validator->validate($this->input(['places_total' => $value, 'places_dispo' => '0']), [1, 2], $this->now);

            $this->assertArrayHasKey('places_total', $errors, 'Valeur testee : "' . $value . '"');
        }
    }

    public function testAvailableSeatsMustBeANonNegativeInteger(): void
    {
        foreach (['-1', 'abc', '', '1.5'] as $value) {
            $errors = $this->validator->validate($this->input(['places_dispo' => $value]), [1, 2], $this->now);

            $this->assertArrayHasKey('places_dispo', $errors, 'Valeur testee : "' . $value . '"');
        }
    }

    public function testMissingFieldsProduceErrorsInsteadOfCrashing(): void
    {
        $errors = $this->validator->validate([], [1, 2], $this->now);

        $this->assertArrayHasKey('id_agence_depart', $errors);
        $this->assertArrayHasKey('date_depart', $errors);
        $this->assertArrayHasKey('places_total', $errors);
    }

    public function testToDataBuildsTheRowToInsert(): void
    {
        $data = $this->validator->toData($this->input(), 7);

        $this->assertSame([
            'id_agence_depart'  => 1,
            'id_agence_arrivee' => 2,
            'date_depart'       => '2030-01-02 08:00:00',
            'date_arrivee'      => '2030-01-02 12:30:00',
            'places_total'      => 4,
            'places_dispo'      => 3,
            'id_employe'        => 7,
        ], $data);
    }

    public function testToDataRefusesInvalidDates(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $this->validator->toData($this->input(['date_depart' => 'nimporte quoi']), 7);
    }
}
