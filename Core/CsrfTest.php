<?php

declare(strict_types=1);

namespace Tests\Core;

use App\Core\Csrf;
use PHPUnit\Framework\TestCase;

/**
 * Teste la protection CSRF (la session est simulee par $_SESSION).
 */
final class CsrfTest extends TestCase
{
    protected function setUp(): void
    {
        $_SESSION = [];
    }

    public function testTokenIsStableDuringASession(): void
    {
        $this->assertSame(Csrf::token(), Csrf::token());
        $this->assertSame(64, strlen(Csrf::token()));
    }

    public function testVerifyAcceptsTheSessionToken(): void
    {
        $this->assertTrue(Csrf::verify(Csrf::token()));
    }

    public function testVerifyRejectsWrongMissingOrEmptyTokens(): void
    {
        Csrf::token();

        $this->assertFalse(Csrf::verify('mauvais'));
        $this->assertFalse(Csrf::verify(''));
        $this->assertFalse(Csrf::verify(null));
    }

    public function testVerifyRejectsEverythingWhenNoTokenWasIssued(): void
    {
        $this->assertFalse(Csrf::verify('nimporte-quoi'));
    }
}
