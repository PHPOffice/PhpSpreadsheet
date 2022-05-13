<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Statistical;

use PhpOffice\PhpSpreadsheet\Calculation\Statistical\Maximum;
use PHPUnit\Framework\TestCase;

class MaxATest extends TestCase
{
    /**
     * @dataProvider providerMAXA
     *
     * @param mixed $expectedResult
     */
    public function testMAXA($expectedResult, ...$args): void
    {
        $result = Maximum::maxA(...$args);
        self::assertEqualsWithDelta($expectedResult, $result, 1E-12);
    }

    public function providerMAXA(): array
    {
        return require 'tests/data/Calculation/Statistical/MAXA.php';
    }
}
