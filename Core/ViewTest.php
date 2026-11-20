<?php

declare(strict_types=1);

namespace Tests\Core;

use App\Core\View;
use PHPUnit\Framework\TestCase;

/**
 * Teste les fonctions d'affichage de View.
 */
final class ViewTest extends TestCase
{
    public function testEscapeNeutralisesHtml(): void
    {
        $this->assertSame('&lt;script&gt;alert(1)&lt;/script&gt;', View::e('<script>alert(1)</script>'));
        $this->assertSame('&quot;a&quot; &amp; &#039;b&#039;', View::e('"a" & \'b\''));
    }

    public function testEscapeConvertsNumbersAndIgnoresArrays(): void
    {
        $this->assertSame('12', View::e(12));
        $this->assertSame('', View::e(['a']));
        $this->assertSame('', View::e(null));
    }

    public function testDateAndTimeAreSplitFromADatetime(): void
    {
        $this->assertSame('15/11/26', View::date('2026-11-15 08:30:00'));
        $this->assertSame('08:30', View::time('2026-11-15 08:30:00'));
    }

    public function testPhoneIsGroupedByPairs(): void
    {
        $this->assertSame('06 12 34 56 78', View::phone('0612345678'));
        $this->assertSame('06 12 34 56 78', View::phone('06.12.34.56.78'));
    }

    public function testPhoneWithAnUnexpectedLengthIsReturnedAsIs(): void
    {
        $this->assertSame('+33 6 12', View::phone('+33 6 12'));
    }
}
