<?php

declare(strict_types=1);

namespace Tests\Core;

use App\Core\Flash;
use PHPUnit\Framework\TestCase;

/**
 * Teste les messages flash (la session est simulee par $_SESSION).
 */
final class FlashTest extends TestCase
{
    protected function setUp(): void
    {
        $_SESSION = [];
    }

    public function testMessagesAreReturnedInOrderThenErased(): void
    {
        Flash::add('success', 'Premier');
        Flash::add('danger', 'Second');

        $this->assertSame(
            [
                ['type' => 'success', 'message' => 'Premier'],
                ['type' => 'danger', 'message' => 'Second'],
            ],
            Flash::consume()
        );
        $this->assertSame([], Flash::consume(), 'Un message flash ne s\'affiche qu\'une fois.');
    }

    public function testConsumeWithoutMessagesReturnsAnEmptyList(): void
    {
        $this->assertSame([], Flash::consume());
    }
}
