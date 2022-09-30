<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Statistical;

use PhpOffice\PhpSpreadsheet\Calculation\Statistical\Minimum;
use PHPUnit\Framework\TestCase;

class MinTest extends TestCase
{
    /**
     * @dataProvider providerMIN
     *
     * @param mixed $expectedResult
     */
    public function testMIN($expectedResult, ...$args): void
    {
        $result = Minimum::min(...$args);
        self::assertEqualsWithDelta($expectedResult, $result, 1E-12);
    }

    public function providerMIN(): array
    {
        return require 'tests/data/Calculation/Statistical/MIN.php';
    }
}
