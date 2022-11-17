<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Statistical;

use PhpOffice\PhpSpreadsheet\Calculation\Statistical;
use PHPUnit\Framework\TestCase;

class DevSqTest extends TestCase
{
    /**
     * @dataProvider providerDEVSQ
     *
     * @param mixed $expectedResult
     */
    public function testDEVSQ($expectedResult, ...$args): void
    {
        $result = Statistical\Deviations::sumSquares(...$args);
        self::assertEqualsWithDelta($expectedResult, $result, 1E-12);
    }

    public function providerDEVSQ(): array
    {
        return require 'tests/data/Calculation/Statistical/DEVSQ.php';
    }
}
