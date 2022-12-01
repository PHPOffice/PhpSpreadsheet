<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Statistical;

use PhpOffice\PhpSpreadsheet\Calculation\Statistical;
use PHPUnit\Framework\TestCase;

// TODO Run test in spreadsheet context
class InterceptTest extends TestCase
{
    /**
     * @dataProvider providerINTERCEPT
     *
     * @param mixed $expectedResult
     */
    public function testINTERCEPT($expectedResult, array $xargs, array $yargs): void
    {
        $result = Statistical\Trends::INTERCEPT($xargs, $yargs);
        self::assertEqualsWithDelta($expectedResult, $result, 1E-12);
    }

    public function providerINTERCEPT(): array
    {
        return require 'tests/data/Calculation/Statistical/INTERCEPT.php';
    }
}
