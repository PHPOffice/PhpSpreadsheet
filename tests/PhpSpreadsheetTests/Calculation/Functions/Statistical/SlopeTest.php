<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Statistical;

use PhpOffice\PhpSpreadsheet\Calculation\Statistical;
use PHPUnit\Framework\TestCase;

// TODO Run test in spreadsheet context.
class SlopeTest extends TestCase
{
    /**
     * @dataProvider providerSLOPE
     *
     * @param mixed $expectedResult
     */
    public function testSLOPE($expectedResult, array $xargs, array $yargs): void
    {
        $result = Statistical\Trends::SLOPE($xargs, $yargs);
        self::assertEqualsWithDelta($expectedResult, $result, 1E-12);
    }

    public function providerSLOPE(): array
    {
        return require 'tests/data/Calculation/Statistical/SLOPE.php';
    }
}
